<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk sistem paket berlangganan
    |
    */

    'package_limits' => [
        'free' => [
            'max_tryouts' => 1,
            'description' => '1 tryout dari semua jenis, unlimited attempts'
        ],
        'kecermatan' => [
            'max_tryouts' => 999, // Unlimited untuk kecermatan (menu terpisah)
            'description' => 'Akses menu kecermatan terpisah'
        ],
        'kecerdasan' => [
            'max_tryouts' => 10,
            'description' => 'Tryout kecerdasan'
        ],
        'kepribadian' => [
            'max_tryouts' => 10,
            'description' => 'Tryout kepribadian'
        ],
        'lengkap' => [
            'max_tryouts' => 20,
            'description' => 'Semua tryout dari semua jenis'
        ]
    ],

    // Mapping untuk backward compatibility dengan menu pricing lama
    'legacy_mapping' => [
        'psikologi' => 'kepribadian', // Menu pricing lama 'psikologi' = 'kepribadian' baru
    ],

    // Package mapping sekarang fully dynamic dari database
    // Tidak perlu hardcode mapping di config
];
