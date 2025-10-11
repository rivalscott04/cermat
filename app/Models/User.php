<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use Notifiable, Impersonate;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'province',
        'regency',
        'role',
        'package' // Tambahkan field package
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konstanta untuk package types
    const PACKAGE_FREE = 'free';
    const PACKAGE_KECERMATAN = 'kecermatan';
    const PACKAGE_KECERDASAN = 'kecerdasan';
    const PACKAGE_KEPRIBADIAN = 'kepribadian';
    const PACKAGE_LENGKAP = 'lengkap';

    public function subscriptions()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function hasActiveSubscription()
    {
        // Jika user sudah di-set is_active, langsung return true
        if ($this->is_active) {
            return true;
        }

        // Cek subscription dengan eager loading untuk menghindari N+1 query
        $subscription = $this->subscriptions;
        
        return $subscription &&
            $subscription->payment_status === 'paid' &&
            $subscription->end_date > now();
    }

    public function hasilTes()
    {
        return $this->hasMany(HasilTes::class);
    }

    public function userTryoutSoal()
    {
        return $this->hasMany(UserTryoutSoal::class);
    }

    public function getPaketAksesAttribute()
    {
        if (!$this->hasActiveSubscription()) {
            return 'free';
        }

        // Handle legacy mapping
        $legacyMapping = config('packages.legacy_mapping', []);
        $userPackage = $this->package ?? 'free';
        
        // Convert legacy package names to new ones
        return $legacyMapping[$userPackage] ?? $userPackage;
    }

    /**
     * Get package limits for current user
     */
    public function getPackageLimits()
    {
        $packageConfig = config('packages.package_limits');
        $userPackage = $this->paket_akses;
        
        return $packageConfig[$userPackage] ?? $packageConfig['free'];
    }

    /**
     * Get maximum tryouts allowed for current user
     */
    public function getMaxTryouts()
    {
        return $this->getPackageLimits()['max_tryouts'];
    }

    /**
     * Get actual number of available tryouts for current user (dynamic from database)
     */
    public function getAvailableTryoutsCount()
    {
        $userPackage = $this->paket_akses;
        
        // Cache the result for 5 minutes to avoid repeated database queries
        return cache()->remember("available_tryouts_count_{$userPackage}", 5 * 60, function () use ($userPackage) {
            $query = \App\Models\Tryout::active()->forUserPackage($userPackage);
            
            if ($userPackage === 'free') {
                // For free users, get 1 tryout per type (kecerdasan, kepribadian, lengkap)
                $all = $query->get();
                $grouped = $all->groupBy('jenis_paket');
                $count = 0;
                foreach (['kecerdasan', 'kepribadian', 'lengkap'] as $jenis) {
                    if ($grouped->has($jenis)) {
                        $count++;
                    }
                }
                return $count;
            } else {
                // For paid packages, get all available tryouts (up to max limit)
                $maxLimit = $this->getMaxTryouts();
                return min($query->count(), $maxLimit);
            }
        });
    }

    /**
     * Get allowed categories for current user (fully dynamic from database)
     */
    public function getAllowedCategories()
    {
        $userPackage = $this->paket_akses;
        
        if ($userPackage === 'free') {
            // OPTIMASI: Cache kategori untuk free users (jarang berubah)
            return cache()->remember('kategori_soal_active_codes', 60 * 24, function () {
                return \App\Models\KategoriSoal::active()->pluck('kode')->toArray();
            });
        }
        
        // OPTIMASI: Cache package categories (jarang berubah)
        return cache()->remember("package_categories_{$userPackage}", 60 * 24, function () use ($userPackage) {
            return \App\Models\PackageCategoryMapping::getCategoriesForPackage($userPackage);
        });
    }

    /**
     * Check if user can access Tes Kecermatan
     */
    public function canAccessKecermatan()
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        return in_array($this->package, [
            self::PACKAGE_KECERMATAN,
            self::PACKAGE_LENGKAP
        ]);
    }

    /**
     * Check if user can access Tryout CBT
     */
    public function canAccessTryout()
    {
        // Free user bisa akses 1 tryout (baik yang tidak punya subscription maupun yang is_active=true tapi package=free)
        if (!$this->hasActiveSubscription() || $this->package === self::PACKAGE_FREE) {
            return true;
        }

        return in_array($this->package, [
            self::PACKAGE_KECERDASAN,
            self::PACKAGE_KEPRIBADIAN,
            self::PACKAGE_LENGKAP
        ]);
    }

    /**
     * Get available menu items based on package
     */
    public function getAvailableMenus()
    {
        $menus = [];

        if ($this->canAccessKecermatan()) {
            $menus[] = 'kecermatan';
        }

        if ($this->canAccessTryout()) {
            $menus[] = 'tryout';
        }

        return $menus;
    }

    /**
     * Check if user can access specific tryout based on package
     */
    public function canAccessSpecificTryout($tryout)
    {
        $userPackage = $this->paket_akses;
        $allowedTypes = $this->getAllowedPackageTypes($userPackage);
        
        return in_array($tryout->jenis_paket, $allowedTypes);
    }

    /**
     * Get allowed package types for user (fully dynamic from database)
     */
    private function getAllowedPackageTypes($userPackage)
    {
        // For FREE users: ambil semua jenis paket yang ada di database
        if ($userPackage === 'free') {
            // OPTIMASI: Cache tryout types untuk free users
            return cache()->remember('tryout_active_types', 60 * 24, function () {
                return \App\Models\Tryout::active()
                    ->distinct()
                    ->pluck('jenis_paket')
                    ->filter()
                    ->toArray();
            });
        }
        
        // OPTIMASI: Cache dynamic mapping
        $dynamicMapping = cache()->remember('package_category_mappings', 60 * 24, function () {
            return \App\Models\PackageCategoryMapping::getAllMappings();
        });
        
        // Standard mapping untuk paket berbayar
        $mapping = [
            'kecerdasan' => ['kecerdasan'],
            'kepribadian' => ['kepribadian'],
            'lengkap' => ['kecerdasan', 'kepribadian', 'lengkap']
        ];
        
        return $mapping[$userPackage] ?? [];
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated()
    {
        return $this->role !== 'admin' && $this->id !== auth()->id();
    }

    /**
     * Check if user can impersonate others
     */
    public function canImpersonate()
    {
        return $this->role === 'admin';
    }

    /**
     * Get paket lengkap completion status
     */
    public function getPaketLengkapStatus()
    {
        if ($this->paket_akses !== self::PACKAGE_LENGKAP) {
            return null;
        }

        $service = app(\App\Services\PaketLengkapService::class);
        return $service->getCompletionStatus($this);
    }

    /**
     * Get paket lengkap dashboard summary
     */
    public function getPaketLengkapSummary()
    {
        if ($this->paket_akses !== self::PACKAGE_LENGKAP) {
            return null;
        }

        $service = app(\App\Services\PaketLengkapService::class);
        return $service->getDashboardSummary($this);
    }

    /**
     * Get paket lengkap progress percentage
     */
    public function getPaketLengkapProgress()
    {
        if ($this->paket_akses !== self::PACKAGE_LENGKAP) {
            return 0;
        }

        $service = app(\App\Services\PaketLengkapService::class);
        return $service->getProgressPercentage($this);
    }

    /**
     * Get package display name
     */
    public function getPackageDisplayName()
    {
        $packageNames = [
            'free' => 'Free',
            'kecermatan' => 'Paket Kecermatan',
            'kecerdasan' => 'Paket Kecerdasan', 
            'kepribadian' => 'Paket Kepribadian',
            'lengkap' => 'Paket Lengkap',
            'psikologi' => 'Psikologi' // Legacy mapping - keep original name for display
        ];

        // Check if user has legacy psikologi package
        $userPackage = $this->package; // Use original package field first
        if ($userPackage === 'psikologi') {
            return $packageNames['psikologi'];
        }
        
        // Use mapped package for other cases
        $userPackage = $this->paket_akses;
        return $packageNames[$userPackage] ?? 'Free';
    }

    /**
     * Get package features description
     */
    public function getPackageFeaturesDescription()
    {
        $features = [
            'free' => [
                'title' => 'Paket Gratis',
                'description' => 'Akses terbatas untuk mencoba sistem',
                'features' => [
                    '1 tryout gratis dari semua jenis',
                    'Akses terbatas ke bank soal',
                    'Riwayat tes dasar'
                ]
            ],
            'kecermatan' => [
                'title' => 'Paket Kecermatan',
                'description' => 'Fokus pada tes kecermatan dan kecepatan',
                'features' => [
                    'Bank soal kecermatan lengkap',
                    'Latihan soal unlimited',
                    'Analisis kecepatan & akurasi',
                    'Timer simulasi ujian',
                    'Riwayat progress harian'
                ]
            ],
            'kecerdasan' => [
                'title' => 'Paket Kecerdasan',
                'description' => 'Tes kecerdasan dan kemampuan kognitif',
                'features' => [
                    'Bank soal TIU, TWK, TKD lengkap',
                    'Tes intelejensi umum',
                    'Tes wawasan kebangsaan',
                    'Tes kemampuan dasar',
                    'Analisis kemampuan kognitif'
                ]
            ],
            'kepribadian' => [
                'title' => 'Paket Kepribadian',
                'description' => 'Tes kepribadian dan psikotes',
                'features' => [
                    'Bank soal TKP, PSIKOTES lengkap',
                    'Tes karakteristik pribadi',
                    'Tes psikotes komprehensif',
                    'Analisis kepribadian',
                    'Tips & strategi psikotes'
                ]
            ],
            'lengkap' => [
                'title' => 'Paket Lengkap',
                'description' => 'Akses penuh ke semua jenis tes',
                'features' => [
                    'Semua fitur Kecermatan',
                    'Semua fitur Kecerdasan',
                    'Semua fitur Kepribadian',
                    'Try out gabungan berkala',
                    'Laporan progress lengkap',
                    'Sertifikat penyelesaian'
                ]
            ]
        ];

        $userPackage = $this->paket_akses; // Use the mapped package
        return $features[$userPackage] ?? $features['free'];
    }

    /**
     * OPTIMASI: Get user statistics with caching
     */
    public function getUserStatistics(): array
    {
        return cache()->remember("user_statistics_{$this->id}", 10, function () {
            // Get latest test date from HasilTes (includes time)
            $latestHasilTes = \App\Models\HasilTes::where('user_id', $this->id)
                ->latest('tanggal_tes')
                ->latest('created_at')
                ->first();
            
            // Get latest tryout activity
            $latestTryout = \App\Models\UserTryoutSession::where('user_id', $this->id)
                ->latest('finished_at')
                ->first();
            
            // Determine the most recent activity between HasilTes and Tryout
            $lastActivity = null;
            if ($latestHasilTes && $latestTryout) {
                $lastActivity = $latestHasilTes->tanggal_tes > $latestTryout->finished_at 
                    ? $latestHasilTes->tanggal_tes 
                    : $latestTryout->finished_at;
            } elseif ($latestHasilTes) {
                $lastActivity = $latestHasilTes->tanggal_tes;
            } elseif ($latestTryout) {
                $lastActivity = $latestTryout->finished_at;
            }
            
            return [
                'total_tryouts' => \App\Models\UserTryoutSession::where('user_id', $this->id)
                    ->where('status', 'completed')
                    ->count(),
                'total_questions_answered' => \App\Models\UserTryoutSoal::where('user_id', $this->id)
                    ->count(),
                'total_kecermatan_tests' => \App\Models\HasilTes::where('user_id', $this->id)
                    ->where('jenis_tes', 'kecermatan')
                    ->count(),
                'last_activity' => $lastActivity,
                'last_test_date' => $latestHasilTes ? $latestHasilTes->tanggal_tes : null
            ];
        });
    }
}
