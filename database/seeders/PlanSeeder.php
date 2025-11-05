<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name'  => 'Hollyn Lite',
                'slug'  => 'hollyn-lite',
                'price' => 5000,
                'features' => [
                    'Disk Space Quota'            => '5 GB',
                    'Max Inodes'                  => '150k',
                    'Monthly Bandwidth'           => '100 GB',
                    'Max FTP Accounts'            => '5',
                    'Max Email Accounts'          => '10',
                    'Max Quota per Mailbox'       => '500 MB',
                    'Max MySQL Databases'         => '5',
                    'Max Subdomains'              => '5',
                    'Max Parked (Aliases)'        => '0',
                    'Max Addon Domains'           => '1',
                    'Hourly Email Limit (domain)' => '100',
                    'Max % Failed/Deferred / hr'  => '10%',
                ],
            ],
            [
                'name'  => 'Hollyn Grow',
                'slug'  => 'hollyn-grow',
                'price' => 12000,
                'features' => [
                    'Disk Space Quota'            => '10 GB',
                    'Max Inodes'                  => '200k',
                    'Monthly Bandwidth'           => '250 GB',
                    'Max FTP Accounts'            => '10',
                    'Max Email Accounts'          => '50',
                    'Max Quota per Mailbox'       => '1 GB',
                    'Max MySQL Databases'         => '10',
                    'Max Subdomains'              => '10',
                    'Max Parked (Aliases)'        => '2',
                    'Max Addon Domains'           => '2',
                    'Hourly Email Limit (domain)' => '200',
                    'Max % Failed/Deferred / hr'  => '10%',
                ],
            ],
            [
                'name'  => 'Hollyn Boost',
                'slug'  => 'hollyn-boost',
                'price' => 25000,
                'features' => [
                    'Disk Space Quota'            => '30 GB',
                    'Max Inodes'                  => '300k',
                    'Monthly Bandwidth'           => '600 GB',
                    'Max FTP Accounts'            => '30',
                    'Max Email Accounts'          => '100',
                    'Max Quota per Mailbox'       => '2 GB',
                    'Max MySQL Databases'         => '30',
                    'Max Subdomains'              => '30',
                    'Max Parked (Aliases)'        => '5',
                    'Max Addon Domains'           => '5',
                    'Hourly Email Limit (domain)' => '400',
                    'Max % Failed/Deferred / hr'  => '8%',
                ],
            ],
            [
                'name'  => 'Hollyn Max',
                'slug'  => 'hollyn-max',
                'price' => 50000,
                'features' => [
                    'Disk Space Quota'            => 'Unlimited',
                    'Max Inodes'                  => 'Unlimited',
                    'Monthly Bandwidth'           => 'Unlimited',
                    'Max FTP Accounts'            => 'Unlimited',
                    'Max Email Accounts'          => 'Unlimited',
                    'Max Quota per Mailbox'       => 'Unlimited',
                    'Max MySQL Databases'         => 'Unlimited',
                    'Max Subdomains'              => 'Unlimited',
                    'Max Parked (Aliases)'        => 'Unlimited',
                    'Max Addon Domains'           => 'Unlimited',
                    'Hourly Email Limit (domain)' => 'Unlimited',
                    'Max % Failed/Deferred / hr'  => '5%',
                ],
            ],
        ];

        foreach ($rows as $r) {
            Plan::updateOrCreate(
                ['slug' => $r['slug']],
                [
                    'name'      => $r['name'],
                    'price_tzs' => $r['price'],
                    'features'  => $r['features'],
                    'is_active' => true,
                ]
            );
        }
    }
}
