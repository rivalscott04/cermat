<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategoryMapping extends Model
{
    protected $fillable = [
        'package_type',
        'kategori_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSoal::class, 'kategori_id');
    }

    /**
     * Get allowed categories for a package type
     */
    public static function getCategoriesForPackage($packageType)
    {
        return self::where('package_type', $packageType)
            ->with('kategori')
            ->get()
            ->pluck('kategori.kode')
            ->toArray();
    }

    /**
     * Get all package mappings as array
     */
    public static function getAllMappings()
    {
        $mappings = [];
        $packageTypes = ['kecerdasan', 'kepribadian', 'lengkap'];
        
        foreach ($packageTypes as $packageType) {
            $mappings[$packageType] = self::getCategoriesForPackage($packageType);
        }
        
        return $mappings;
    }

    /**
     * Update mappings for a package type
     */
    public static function updateMappings($packageType, $kategoriIds)
    {
        // Delete existing mappings for this package
        self::where('package_type', $packageType)->delete();
        
        // Insert new mappings
        $mappings = [];
        foreach ($kategoriIds as $kategoriId) {
            $mappings[] = [
                'package_type' => $packageType,
                'kategori_id' => $kategoriId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($mappings)) {
            self::insert($mappings);
        }
    }
}
