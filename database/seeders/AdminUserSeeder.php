<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@hollyn.local';
        $pass  = 'ChangeMe123!'; // BADILISHA BAADA YA KUINGIA

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => 'Hollyn Admin',
                'password'          => Hash::make($pass),
                'role'              => 'admin',
                'phone'             => null,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Admin ready: $email / $pass");
    }
}
