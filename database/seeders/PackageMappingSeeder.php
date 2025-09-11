<?php

namespace Database\Seeders;

use App\Models\PackageCategoryMapping;
use App\Models\KategoriSoal;
use Illuminate\Database\Seeder;

class PackageMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default package mappings
        $defaultMappings = [
            'free' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD'],
            'kecerdasan' => ['TIU', 'TWK', 'TKD'],
            'kepribadian' => ['TKP', 'PSIKOTES'],
            'lengkap' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD']
        ];

        foreach ($defaultMappings as $packageType => $kategoriCodes) {
            $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
            PackageCategoryMapping::updateMappings($packageType, $kategoriIds);
        }
    }
}
