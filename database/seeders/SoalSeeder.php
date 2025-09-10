<?php

namespace Database\Seeders;

use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $kategoris = KategoriSoal::all();
        
        // Define question types
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];
        
        // Define levels
        $levels = ['mudah', 'sedang', 'sulit'];
        
        // Sample questions data for each category
        $questionsData = [
            'TWK' => [
                'mudah' => [
                    'benar_salah' => [
                        'Pancasila adalah dasar negara Indonesia yang terdiri dari 5 sila.',
                        'UUD 1945 adalah konstitusi negara Indonesia yang berlaku saat ini.'
                    ],
                    'pg_satu' => [
                        'Berapa jumlah sila dalam Pancasila?',
                        'Siapa yang menciptakan lagu Indonesia Raya?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling penting dalam kehidupan berbangsa?',
                        'Nilai-nilai Pancasila yang paling fundamental adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan lambang negara Indonesia:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan provinsi di Pulau Jawa:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar bendera negara Indonesia:',
                        'Identifikasikan gambar lambang negara Garuda Pancasila:'
                    ]
                ],
                'sedang' => [
                    'benar_salah' => [
                        'Pembukaan UUD 1945 terdiri dari 4 alinea.',
                        'Sistem pemerintahan Indonesia adalah presidensial.'
                    ],
                    'pg_satu' => [
                        'Alinea ke berapa dalam Pembukaan UUD 1945 yang menyebutkan tujuan negara?',
                        'Lembaga negara yang berwenang mengubah UUD 1945 adalah?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan karakteristik bangsa Indonesia?',
                        'Nilai-nilai yang paling menonjol dalam kehidupan bermasyarakat Indonesia adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan hak asasi manusia:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan kewajiban warga negara:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar peta wilayah Indonesia:',
                        'Identifikasikan gambar tokoh proklamator kemerdekaan:'
                    ]
                ],
                'sulit' => [
                    'benar_salah' => [
                        'Amandemen UUD 1945 telah dilakukan sebanyak 4 kali.',
                        'Mahkamah Konstitusi berwenang menguji undang-undang terhadap UUD 1945.'
                    ],
                    'pg_satu' => [
                        'Berapa kali UUD 1945 telah diamandemen?',
                        'Lembaga negara yang berwenang menguji konstitusionalitas undang-undang adalah?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan sistem ketatanegaraan Indonesia?',
                        'Nilai-nilai yang paling fundamental dalam pembentukan karakter bangsa adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan prinsip-prinsip demokrasi:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan ciri-ciri negara hukum:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar gedung MPR/DPR:',
                        'Identifikasikan gambar simbol-simbol kenegaraan Indonesia:'
                    ]
                ]
            ],
            'TIU' => [
                'mudah' => [
                    'benar_salah' => [
                        '2 + 2 = 4 adalah pernyataan yang benar.',
                        'Jakarta adalah ibu kota negara Indonesia.'
                    ],
                    'pg_satu' => [
                        'Berapa hasil dari 5 + 3?',
                        'Apa sinonim dari kata "besar"?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat untuk menggambarkan kemampuan verbal?',
                        'Nilai yang paling penting dalam kemampuan numerik adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan operasi matematika dasar:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan jenis-jenis kata:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan pola matematika:',
                        'Identifikasikan gambar yang menunjukkan hubungan logis:'
                    ]
                ],
                'sedang' => [
                    'benar_salah' => [
                        'Logika matematika menggunakan prinsip-prinsip deduktif.',
                        'Kemampuan verbal meliputi pemahaman kata dan kalimat.'
                    ],
                    'pg_satu' => [
                        'Jika A = 5 dan B = 3, berapa nilai A + B?',
                        'Apa antonim dari kata "panjang"?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kemampuan analitis?',
                        'Nilai yang paling penting dalam pemecahan masalah adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan kemampuan kognitif:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan jenis penalaran:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan pola geometri:',
                        'Identifikasikan gambar yang menunjukkan urutan logis:'
                    ]
                ],
                'sulit' => [
                    'benar_salah' => [
                        'Kemampuan numerik meliputi pemahaman konsep matematika abstrak.',
                        'Logika formal menggunakan simbol-simbol matematika.'
                    ],
                    'pg_satu' => [
                        'Jika x + y = 10 dan x - y = 2, berapa nilai x?',
                        'Apa analogi yang tepat untuk "pena : menulis"?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kemampuan sintesis?',
                        'Nilai yang paling penting dalam kemampuan evaluasi adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan kemampuan berpikir tingkat tinggi:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan jenis-jenis logika:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan pola matematika kompleks:',
                        'Identifikasikan gambar yang menunjukkan hubungan kausal:'
                    ]
                ]
            ],
            'TKP' => [
                'mudah' => [
                    'benar_salah' => [
                        'Integritas adalah kejujuran dan konsistensi dalam bertindak.',
                        'Kerjasama tim sangat penting dalam lingkungan kerja.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam membangun kepercayaan?',
                        'Bagaimana cara terbaik menghadapi konflik di tempat kerja?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kepemimpinan yang baik?',
                        'Nilai yang paling penting dalam etika kerja adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan karakteristik pemimpin:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan nilai-nilai profesional:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan kerjasama tim:',
                        'Identifikasikan gambar yang menunjukkan kepemimpinan:'
                    ]
                ],
                'sedang' => [
                    'benar_salah' => [
                        'Adaptabilitas adalah kemampuan menyesuaikan diri dengan perubahan.',
                        'Komunikasi efektif memerlukan kemampuan mendengarkan yang baik.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam mengelola stres kerja?',
                        'Bagaimana cara terbaik memberikan feedback kepada rekan kerja?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan manajemen waktu yang efektif?',
                        'Nilai yang paling penting dalam pengambilan keputusan adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan keterampilan interpersonal:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan karakteristik profesional:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan manajemen konflik:',
                        'Identifikasikan gambar yang menunjukkan komunikasi efektif:'
                    ]
                ],
                'sulit' => [
                    'benar_salah' => [
                        'Emotional intelligence mempengaruhi efektivitas kepemimpinan.',
                        'Cultural intelligence penting dalam lingkungan kerja multikultural.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam mengembangkan emotional intelligence?',
                        'Bagaimana cara terbaik mengelola tim yang beragam?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan transformational leadership?',
                        'Nilai yang paling penting dalam organizational behavior adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan kompetensi kepemimpinan:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan faktor motivasi kerja:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan organizational culture:',
                        'Identifikasikan gambar yang menunjukkan team dynamics:'
                    ]
                ]
            ],
            'PSIKOTES' => [
                'mudah' => [
                    'benar_salah' => [
                        'Psikotes mengukur kemampuan kognitif dan kepribadian.',
                        'Konsentrasi adalah kemampuan memfokuskan perhatian pada satu hal.'
                    ],
                    'pg_satu' => [
                        'Apa yang diukur dalam tes kemampuan verbal?',
                        'Bagaimana cara meningkatkan daya ingat jangka pendek?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kemampuan kognitif?',
                        'Nilai yang paling penting dalam tes psikologi adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan jenis tes psikologi:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan kemampuan kognitif:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan pola visual:',
                        'Identifikasikan gambar yang menunjukkan kemampuan spasial:'
                    ]
                ],
                'sedang' => [
                    'benar_salah' => [
                        'Working memory adalah kemampuan menyimpan informasi sementara.',
                        'Cognitive flexibility mempengaruhi kemampuan problem solving.'
                    ],
                    'pg_satu' => [
                        'Apa yang diukur dalam tes kemampuan numerik?',
                        'Bagaimana cara mengembangkan kemampuan analitis?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan executive function?',
                        'Nilai yang paling penting dalam cognitive assessment adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan komponen working memory:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan jenis attention:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan cognitive load:',
                        'Identifikasikan gambar yang menunjukkan mental rotation:'
                    ]
                ],
                'sulit' => [
                    'benar_salah' => [
                        'Metacognition adalah kemampuan berpikir tentang berpikir.',
                        'Fluid intelligence berkurang seiring bertambahnya usia.'
                    ],
                    'pg_satu' => [
                        'Apa yang diukur dalam tes kemampuan abstrak?',
                        'Bagaimana cara mengembangkan metacognitive skills?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan cognitive architecture?',
                        'Nilai yang paling penting dalam intelligence testing adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan teori intelligence:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan cognitive processes:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan cognitive mapping:',
                        'Identifikasikan gambar yang menunjukkan neural networks:'
                    ]
                ]
            ],
            'TKD' => [
                'mudah' => [
                    'benar_salah' => [
                        'Kemampuan dasar meliputi membaca, menulis, dan berhitung.',
                        'Literasi adalah kemampuan membaca dan memahami teks.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam kemampuan dasar?',
                        'Bagaimana cara mengembangkan kemampuan numerik dasar?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kemampuan dasar?',
                        'Nilai yang paling penting dalam pembelajaran dasar adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan kemampuan dasar:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan keterampilan fundamental:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan kemampuan dasar:',
                        'Identifikasikan gambar yang menunjukkan pembelajaran:'
                    ]
                ],
                'sedang' => [
                    'benar_salah' => [
                        'Kemampuan dasar diperlukan untuk mengembangkan kemampuan lanjutan.',
                        'Problem solving memerlukan kemampuan analitis yang baik.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam mengembangkan kemampuan dasar?',
                        'Bagaimana cara meningkatkan kemampuan pemecahan masalah?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan kemampuan analitis?',
                        'Nilai yang paling penting dalam critical thinking adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan kemampuan analitis:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan keterampilan problem solving:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan problem solving:',
                        'Identifikasikan gambar yang menunjukkan critical thinking:'
                    ]
                ],
                'sulit' => [
                    'benar_salah' => [
                        'Higher-order thinking skills meliputi analisis, sintesis, dan evaluasi.',
                        'Metacognitive strategies meningkatkan efektivitas pembelajaran.'
                    ],
                    'pg_satu' => [
                        'Apa yang paling penting dalam mengembangkan higher-order thinking?',
                        'Bagaimana cara mengintegrasikan berbagai kemampuan dasar?'
                    ],
                    'pg_bobot' => [
                        'Manakah yang paling tepat menggambarkan complex problem solving?',
                        'Nilai yang paling penting dalam advanced cognitive skills adalah?'
                    ],
                    'pg_pilih_2' => [
                        'Pilihlah 2 dari 4 pilihan yang merupakan higher-order thinking:',
                        'Pilihlah 2 dari 4 pilihan yang merupakan advanced skills:'
                    ],
                    'gambar' => [
                        'Identifikasikan gambar yang menunjukkan complex reasoning:',
                        'Identifikasikan gambar yang menunjukkan advanced cognition:'
                    ]
                ]
            ]
        ];

        // Sample options for different question types
        $optionsData = [
            'benar_salah' => [
                ['opsi' => 'A', 'teks' => 'Benar', 'bobot' => 1.0],
                ['opsi' => 'B', 'teks' => 'Salah', 'bobot' => 0.0]
            ],
            'pg_satu' => [
                ['opsi' => 'A', 'teks' => 'Pilihan A', 'bobot' => 1.0],
                ['opsi' => 'B', 'teks' => 'Pilihan B', 'bobot' => 0.0],
                ['opsi' => 'C', 'teks' => 'Pilihan C', 'bobot' => 0.0],
                ['opsi' => 'D', 'teks' => 'Pilihan D', 'bobot' => 0.0]
            ],
            'pg_bobot' => [
                ['opsi' => 'A', 'teks' => 'Sangat Setuju', 'bobot' => 1.0],
                ['opsi' => 'B', 'teks' => 'Setuju', 'bobot' => 0.75],
                ['opsi' => 'C', 'teks' => 'Tidak Setuju', 'bobot' => 0.25],
                ['opsi' => 'D', 'teks' => 'Sangat Tidak Setuju', 'bobot' => 0.0]
            ],
            'pg_pilih_2' => [
                ['opsi' => 'A', 'teks' => 'Pilihan A', 'bobot' => 0.5],
                ['opsi' => 'B', 'teks' => 'Pilihan B', 'bobot' => 0.5],
                ['opsi' => 'C', 'teks' => 'Pilihan C', 'bobot' => 0.0],
                ['opsi' => 'D', 'teks' => 'Pilihan D', 'bobot' => 0.0]
            ],
            'gambar' => [
                ['opsi' => 'A', 'teks' => 'Gambar A', 'bobot' => 1.0],
                ['opsi' => 'B', 'teks' => 'Gambar B', 'bobot' => 0.0],
                ['opsi' => 'C', 'teks' => 'Gambar C', 'bobot' => 0.0],
                ['opsi' => 'D', 'teks' => 'Gambar D', 'bobot' => 0.0]
            ]
        ];

        // Sample correct answers
        $correctAnswers = [
            'benar_salah' => 'A',
            'pg_satu' => 'A',
            'pg_bobot' => 'A',
            'pg_pilih_2' => 'A,B',
            'gambar' => 'A'
        ];

        // Sample explanations
        $explanations = [
            'TWK' => 'Penjelasan terkait wawasan kebangsaan dan nilai-nilai Pancasila.',
            'TIU' => 'Penjelasan terkait kemampuan intelektual dan logika.',
            'TKP' => 'Penjelasan terkait karakteristik pribadi dan kepribadian.',
            'PSIKOTES' => 'Penjelasan terkait kemampuan kognitif dan psikologis.',
            'TKD' => 'Penjelasan terkait kemampuan dasar dan fundamental.'
        ];

        $questionCounter = 1;

        // Generate questions for each category, level, and type
        foreach ($kategoris as $kategori) {
            $kategoriCode = $kategori->kode;
            
            if (!isset($questionsData[$kategoriCode])) {
                continue;
            }

            foreach ($levels as $level) {
                if (!isset($questionsData[$kategoriCode][$level])) {
                    continue;
                }

                foreach ($tipes as $tipe) {
                    if (!isset($questionsData[$kategoriCode][$level][$tipe])) {
                        continue;
                    }

                    $questions = $questionsData[$kategoriCode][$level][$tipe];
                    
                    // Create 2 questions for each combination
                    for ($i = 0; $i < 2; $i++) {
                        if (!isset($questions[$i])) {
                            continue;
                        }

                        $soal = Soal::create([
                            'pertanyaan' => $questions[$i],
                            'tipe' => $tipe,
                            'level' => $level,
                            'kategori_id' => $kategori->id,
                            'pembahasan' => $explanations[$kategoriCode],
                            'pembahasan_type' => 'text',
                            'jawaban_benar' => $correctAnswers[$tipe],
                            'is_active' => true
                        ]);

                        // Create options for the question
                        if (isset($optionsData[$tipe])) {
                            foreach ($optionsData[$tipe] as $optionData) {
                                OpsiSoal::create([
                                    'soal_id' => $soal->id,
                                    'opsi' => $optionData['opsi'],
                                    'teks' => $optionData['teks'],
                                    'bobot' => $optionData['bobot']
                                ]);
                            }
                        }

                        $questionCounter++;
                    }
                }
            }
        }

        $this->command->info("Created {$questionCounter} sample questions across all categories, levels, and types.");
    }
}
