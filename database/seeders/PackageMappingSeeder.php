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
        // Dynamic package mappings based on available categories
        $packageTypes = ['free', 'kecermatan', 'kecerdasan', 'kepribadian', 'lengkap'];
        
        foreach ($packageTypes as $packageType) {
            $kategoriCodes = $this->getPackageMapping($packageType);
            
            if (!empty($kategoriCodes)) {
                $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
                PackageCategoryMapping::updateMappings($packageType, $kategoriIds);
            }
        }
    }

    /**
     * Get package mapping based on package type (dynamic)
     */
    private function getPackageMapping($packageType)
    {
        // Get all available categories from database
        $allCategories = KategoriSoal::active()->pluck('kode')->toArray();
        
        switch ($packageType) {
            case 'free':
                // FREE bisa akses semua kategori yang ada
                return $allCategories;
                
            case 'kecermatan':
                // Kecermatan: ambil kategori yang mengandung 'KECERMATAN'
                return array_filter($allCategories, function($cat) {
                    return strpos($cat, 'KECERMATAN') !== false;
                });
                
            case 'kecerdasan':
                // Kecerdasan: ambil kategori TIU, TWK, TKD
                return array_filter($allCategories, function($cat) {
                    return in_array($cat, ['TIU', 'TWK', 'TKD']);
                });
                
            case 'kepribadian':
                // Kepribadian: ambil kategori TKP, PSIKOTES
                return array_filter($allCategories, function($cat) {
                    return in_array($cat, ['TKP', 'PSIKOTES']);
                });
                
            case 'lengkap':
                // Lengkap: ambil semua kategori kecuali KECERMATAN (karena menu terpisah)
                return array_filter($allCategories, function($cat) {
                    return strpos($cat, 'KECERMATAN') === false;
                });
                
            default:
                return [];
        }
    }
}
