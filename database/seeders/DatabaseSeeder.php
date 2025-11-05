<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---- Guards & pre-checks -------------------------------------------
        if (!Schema::hasTable('migrations')) {
            $this->command?->warn("⚠️  Hakuna jedwali la 'migrations'. Kimbiza: php artisan migrate");
            return;
        }

        $requiredTables = ['users', 'plans', 'orders', 'services'];
        $missing = collect($requiredTables)->reject(fn ($t) => Schema::hasTable($t));
        if ($missing->isNotEmpty()) {
            $this->command?->warn("⚠️  Meza hazipo: " . $missing->implode(', '));
            $this->command?->warn("    Kimbiza: php artisan migrate");
            return;
        }

        // Onyo ndogo production
        if (App::environment('production')) {
            $this->command?->warn('🛡️  Unaseed kwenye PRODUCTION. Hakikisha unajua unachofanya.');
        }

        // ---- Optional controls via .env (si lazima) ------------------------
        $skipPlans    = (bool) env('SEED_SKIP_PLANS', false);
        $skipAdmin    = (bool) env('SEED_SKIP_ADMIN', false);
        $forcePlans   = (bool) env('SEED_FORCE_PLANS', false);   // husafisha plans kabla ya seeding
        $truncateDev  = (bool) env('SEED_TRUNCATE', false);      // TRUNCATE baadhi ya meza (dev tu)

        // ---- Optional truncate on local/dev --------------------------------
        if (!App::environment('production') && $truncateDev) {
            $this->command?->warn('🧹 Truncating selected tables (dev only)…');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (['orders', 'services', 'payment_events'] as $tbl) {
                if (Schema::hasTable($tbl)) {
                    try { DB::table($tbl)->truncate(); } catch (\Throwable $e) {}
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info('🚀 Kuanza seeding…');

        // ---- Plans ----------------------------------------------------------
        $this->seedWrapper(!$skipPlans, '🟡 Seeding: PlanSeeder', function () use ($forcePlans) {
            if ($forcePlans) {
                $this->command?->warn('↻  Forcing reseed for plans (clearing table)…');
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table('plans')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            // class_exists guard
            if (!class_exists(\Database\Seeders\PlanSeeder::class)) {
                $this->command?->error('   ❌ PlanSeeder haipatikani. Hakikisha file: database/seeders/PlanSeeder.php');
                return;
            }

            $this->call(\Database\Seeders\PlanSeeder::class);
        });

        // ---- Admin user -----------------------------------------------------
        $this->seedWrapper(!$skipAdmin, '🟢 Seeding: AdminUserSeeder', function () {
            if (!class_exists(\Database\Seeders\AdminUserSeeder::class)) {
                $this->command?->error('   ❌ AdminUserSeeder haipatikani. Hakikisha file: database/seeders/AdminUserSeeder.php');
                return;
            }
            $this->call(\Database\Seeders\AdminUserSeeder::class);
        });

        // ---- Extra seeders (ongeza hapa ukihitaji) -------------------------
        // $this->seedWrapper(true, '📦 Seeding: SampleDataSeeder', fn () => $this->call(\Database\Seeders\SampleDataSeeder::class));

        $this->command?->info('✅ Seeding complete.');
    }

    /**
     * Helper: run a seeder with console messages + try/catch + transaction.
     *
     * @param  bool     $shouldRun  Ruhusu kuruka seeder (env flag)
     * @param  string   $label      Ujumbe wa console
     * @param  callable $callback   $this->call(SomeSeeder::class)
     */
    protected function seedWrapper(bool $shouldRun, string $label, callable $callback): void
    {
        if (!$shouldRun) {
            $this->command?->warn("⏭️  Skipping: " . $label);
            return;
        }

        $this->command?->line($label);
        $start = microtime(true);

        try {
            DB::transaction(fn () => $callback(), 1);
            $elapsed = number_format(microtime(true) - $start, 2);
            $this->command?->info("   ✅ Done ({$elapsed}s)");
        } catch (\Throwable $e) {
            $this->command?->error("   ❌ Fail: " . $e->getMessage());
            // usi-throw ili seeders zingine ziendelee
        }
    }
}
