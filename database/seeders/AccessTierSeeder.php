<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccessTier;

class AccessTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['key' => 'kecermatan', 'name' => 'Kecermatan'],
            ['key' => 'kepribadian', 'name' => 'Kepribadian'],
            ['key' => 'kecerdasan', 'name' => 'Kecerdasan'],
            ['key' => 'lengkap', 'name' => 'Lengkap'],
            ['key' => 'free', 'name' => 'Free'],
        ];

        foreach ($tiers as $tier) {
            AccessTier::firstOrCreate(['key' => $tier['key']], $tier);
        }
    }
}


