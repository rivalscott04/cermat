# AKADEMIK MODULE - IMPLEMENTATION GUIDE

## OVERVIEW
This document provides a comprehensive walkthrough for implementing the AKADEMIK module in the CERMAT POLDA system. The AKADEMIK module is a parallel system to the existing PSIKOLOGI module, with separate package types and category mappings.

## SYSTEM ARCHITECTURE

### 1. INDUK PAKET (2 TIPE UTAMA)
- **PSIKOLOGI** (existing)
  - `kecermatan` (paket)
  - `kecerdasan` (paket) 
  - `kepribadian` (paket)
  - `lengkap` (paket)

- **AKADEMIK** (new)
  - `bahasa_inggris` (paket)
  - `pu` (paket) - Pengetahuan Umum
  - `twk` (paket) - Tes Wawasan Kebangsaan
  - `numerik` (paket)

### 2. KATEGORI SOAL DINAMIS
- Categories are **DYNAMIC** and configurable by admin
- Mapping is handled through `PackageCategoryMapping` model
- No hardcoded categories - fully database-driven

## IMPLEMENTATION STEPS

### STEP 1: UPDATE CONFIG PACKAGES.PHP
**File:** `config/packages.php`

**Action:** Add AKADEMIK package limits
```php
'package_limits' => [
    // ... existing PSIKOLOGI packages
    
    // AKADEMIK packages (new)
    'bahasa_inggris' => [
        'max_tryouts' => 10,
        'description' => 'Tryout bahasa Inggris'
    ],
    'pu' => [
        'max_tryouts' => 10,
        'description' => 'Tryout pengetahuan umum'
    ],
    'twk' => [
        'max_tryouts' => 10,
        'description' => 'Tryout tes wawasan kebangsaan'
    ],
    'numerik' => [
        'max_tryouts' => 10,
        'description' => 'Tryout numerik'
    ]
]
```

### STEP 2: UPDATE PACKAGE MAPPING SEEDER
**File:** `database/seeders/PackageMappingSeeder.php`

**Action:** Add AKADEMIK mappings
```php
// AKADEMIK mappings (new) - KATEGORI DINAMIS
$akademikMappings = [
    'bahasa_inggris' => ['BAHASA_INGGRIS'],
    'pu' => ['PU'],
    'twk' => ['TWK'],
    'numerik' => ['NUMERIK']
];

// Seed AKADEMIK mappings
foreach ($akademikMappings as $packageType => $kategoriCodes) {
    $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
    PackageCategoryMapping::updateMappings($packageType, $kategoriCodes);
}
```

### STEP 3: UPDATE CONTROLLERS FOR TYPE FILTERING
**Files:** 
- `app/Http/Controllers/KategoriSoalController.php`
- `app/Http/Controllers/SoalController.php`

**Action:** Add type parameter support
```php
public function index(Request $request)
{
    $type = $request->get('type', 'psikologi'); // psikologi atau akademik
    
    // Kategori soal tetap sama, hanya filter berdasarkan induk
    $kategoris = KategoriSoal::orderBy('nama')->paginate(20);
    
    return view('admin.kategori.index', compact('kategoris', 'type'));
}
```

### STEP 4: UPDATE ADMIN MENU
**File:** `resources/views/components/sidenav.blade.php`

**Action:** Add separate menu for AKADEMIK
```php
<!-- Master Soal PSIKOLOGI -->
<li>
    <a href="#"><i class="fa fa-brain"></i> <span class="nav-label">Master Soal PSIKOLOGI</span> <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        <li>
            <a href="{{ route('admin.kategori.index', ['type' => 'psikologi']) }}">
                <i class="fa fa-tags"></i>
                <span class="nav-label">Kategori Soal</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.soal.index', ['type' => 'psikologi']) }}">
                <i class="fa fa-question-circle"></i>
                <span class="nav-label">Soal</span>
            </a>
        </li>
    </ul>
</li>

<!-- Master Soal AKADEMIK -->
<li>
    <a href="#"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Master Soal AKADEMIK</span> <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        <li>
            <a href="{{ route('admin.kategori.index', ['type' => 'akademik']) }}">
                <i class="fa fa-tags"></i>
                <span class="nav-label">Kategori Soal</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.soal.index', ['type' => 'akademik']) }}">
                <i class="fa fa-question-circle"></i>
                <span class="nav-label">Soal</span>
            </a>
        </li>
    </ul>
</li>
```

### STEP 5: UPDATE SUBSCRIPTION CONTROLLER
**File:** `app/Http/Controllers/SubscriptionController.php`

**Action:** Add AKADEMIK package details
```php
$packageDetails = [
    // ... existing PSIKOLOGI packages
    
    // AKADEMIK details (new)
    'bahasa_inggris' => [
        'name' => 'Paket Bahasa Inggris',
        'description' => 'Fokus Tes Bahasa Inggris',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal bahasa Inggris lengkap',
            'Tes kemampuan bahasa Inggris',
            'Analisis kemampuan linguistik',
            'Timer simulasi ujian',
            'Riwayat progress harian'
        ]
    ],
    'pu' => [
        'name' => 'Paket Pengetahuan Umum',
        'description' => 'Fokus Tes Pengetahuan Umum',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal pengetahuan umum lengkap',
            'Tes pengetahuan umum',
            'Analisis kemampuan kognitif',
            'Timer simulasi ujian',
            'Riwayat progress harian'
        ]
    ],
    'twk' => [
        'name' => 'Paket TWK',
        'description' => 'Fokus Tes Wawasan Kebangsaan',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal TWK lengkap',
            'Tes wawasan kebangsaan',
            'Analisis kemampuan kognitif',
            'Timer simulasi ujian',
            'Riwayat progress harian'
        ]
    ],
    'numerik' => [
        'name' => 'Paket Numerik',
        'description' => 'Fokus Tes Numerik',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal numerik lengkap',
            'Tes kemampuan numerik',
            'Analisis kemampuan kognitif',
            'Timer simulasi ujian',
            'Riwayat progress harian'
        ]
    ]
];
```

### STEP 6: UPDATE VIEWS FOR CONTEXT AWARENESS
**Files:** 
- `resources/views/admin/kategori/index.blade.php`
- `resources/views/admin/soal/index.blade.php`

**Action:** Add type-aware display
```php
<div class="ibox-title">
    <h5>
        @if($type === 'psikologi')
            <i class="fa fa-brain"></i> Kategori Soal PSIKOLOGI
        @elseif($type === 'akademik')
            <i class="fa fa-graduation-cap"></i> Kategori Soal AKADEMIK
        @endif
    </h5>
    <div class="ibox-tools">
        <a href="{{ route('admin.kategori.create', ['type' => $type]) }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Kategori
        </a>
    </div>
</div>
```

### STEP 7: UPDATE ROUTES
**File:** `routes/web.php`

**Action:** Ensure routes support type parameter
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... existing routes
    
    // CBT Admin Routes with type parameter
    Route::get('kategori', [KategoriSoalController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [KategoriSoalController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriSoalController::class, 'store'])->name('kategori.store');
    // ... other routes
    
    Route::get('soal', [SoalController::class, 'index'])->name('soal.index');
    Route::get('soal/create', [SoalController::class, 'create'])->name('soal.create');
    Route::post('soal', [SoalController::class, 'store'])->name('soal.store');
    // ... other routes
});
```

## TODO LIST

### PHASE 1: CONFIGURATION (1-2 hours)
- [ ] Update `config/packages.php` with AKADEMIK package limits
- [ ] Update `database/seeders/PackageMappingSeeder.php` with AKADEMIK mappings
- [ ] Test configuration changes

### PHASE 2: CONTROLLER UPDATES (2-3 hours)
- [ ] Update `KategoriSoalController.php` for type filtering
- [ ] Update `SoalController.php` for type filtering
- [ ] Update `SubscriptionController.php` with AKADEMIK package details
- [ ] Test controller functionality

### PHASE 3: VIEW UPDATES (2-3 hours)
- [ ] Update `sidenav.blade.php` with AKADEMIK menu
- [ ] Update `kategori/index.blade.php` for context awareness
- [ ] Update `soal/index.blade.php` for context awareness
- [ ] Update `kategori/create.blade.php` for type support
- [ ] Update `soal/create.blade.php` for type support
- [ ] Test view functionality

### PHASE 4: ROUTE UPDATES (1 hour)
- [ ] Update `routes/web.php` to support type parameter
- [ ] Test route functionality

### PHASE 5: TESTING (2-3 hours)
- [ ] Test admin menu navigation
- [ ] Test category creation for both types
- [ ] Test soal creation for both types
- [ ] Test package mapping functionality
- [ ] Test subscription flow for AKADEMIK packages

### PHASE 6: DEPLOYMENT (1 hour)
- [ ] Run database migrations
- [ ] Run seeders
- [ ] Clear cache
- [ ] Test production functionality

## KEY PRINCIPLES

### 1. DYNAMIC SYSTEM
- No hardcoded categories
- All mappings stored in database
- Admin can configure mappings through interface

### 2. SEPARATION OF CONCERNS
- PSIKOLOGI and AKADEMIK are separate systems
- Same underlying logic, different configurations
- Clear menu separation for admin

### 3. MAINTAINABILITY
- Code reuse between PSIKOLOGI and AKADEMIK
- Type parameter for filtering
- Context-aware views

### 4. SCALABILITY
- Easy to add new package types
- Easy to add new categories
- Flexible mapping system

## TESTING CHECKLIST

### ADMIN FUNCTIONALITY
- [ ] Can navigate to PSIKOLOGI menu
- [ ] Can navigate to AKADEMIK menu
- [ ] Can create categories for both types
- [ ] Can create soal for both types
- [ ] Can view package mappings

### USER FUNCTIONALITY
- [ ] Can see AKADEMIK packages in subscription
- [ ] Can purchase AKADEMIK packages
- [ ] Can access AKADEMIK tryouts
- [ ] Scoring works for AKADEMIK categories

### SYSTEM FUNCTIONALITY
- [ ] Package mapping works correctly
- [ ] Category filtering works correctly
- [ ] Soal filtering works correctly
- [ ] Payment integration works for AKADEMIK

## NOTES

1. **No Database Changes Required**: The existing database structure supports the new system
2. **Backward Compatibility**: Existing PSIKOLOGI functionality remains unchanged
3. **Dynamic Categories**: Admin can add new categories without code changes
4. **Flexible Mapping**: Package-to-category mapping can be configured through admin interface

## ESTIMATED TOTAL TIME: 8-12 hours

This implementation maintains the existing system's flexibility while adding the new AKADEMIK module with clear separation and maintainability.
