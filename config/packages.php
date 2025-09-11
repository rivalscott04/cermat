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
            'allowed_categories' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD'],
            'description' => '1 tryout dari semua jenis, unlimited attempts'
        ],
        'kecermatan' => [
            'max_tryouts' => 999, // Unlimited untuk kecermatan (menu terpisah)
            'allowed_categories' => ['KECERMATAN'],
            'description' => 'Akses menu kecermatan terpisah'
        ],
        'kecerdasan' => [
            'max_tryouts' => 10,
            'allowed_categories' => ['TIU', 'TWK', 'TKD'],
            'description' => 'Tryout kecerdasan (TIU, TWK, TKD)'
        ],
        'kepribadian' => [
            'max_tryouts' => 10,
            'allowed_categories' => ['TKP', 'PSIKOTES'],
            'description' => 'Tryout kepribadian (TKP, PSIKOTES)'
        ],
        'lengkap' => [
            'max_tryouts' => 20,
            'allowed_categories' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD'],
            'description' => 'Semua tryout dari semua jenis'
        ]
    ],

    // Mapping untuk backward compatibility dengan menu pricing lama
    'legacy_mapping' => [
        'psikologi' => 'kepribadian', // Menu pricing lama 'psikologi' = 'kepribadian' baru
    ],

    'package_mapping' => [
        'kecerdasan' => ['TIU', 'TWK', 'TKD'],
        'kepribadian' => ['TKP', 'PSIKOTES'],
        'lengkap' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD'],
        'free' => ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD']
    ]
];
