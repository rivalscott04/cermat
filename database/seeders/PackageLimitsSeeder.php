<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageLimitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageLimits = [
            [
                'package_type' => 'free',
                'max_tryouts' => 1,
                'allowed_categories' => json_encode(['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'kecermatan',
                'max_tryouts' => 999, // Unlimited untuk kecermatan (menu terpisah)
                'allowed_categories' => json_encode(['KECERMATAN']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'kecerdasan',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['TIU', 'TWK', 'TKD']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'kepribadian',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['TKP', 'PSIKOTES']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'lengkap',
                'max_tryouts' => 20,
                'allowed_categories' => json_encode(['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD']),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('package_limits')->insert($packageLimits);
    }
}
