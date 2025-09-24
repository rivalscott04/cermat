<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use App\Models\PackageCategoryMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $query = Soal::with(['kategori', 'opsi']);

        // Apply kategori filter
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        // Apply tipe filter
        if ($request->filled('tipe')) {
            $query->byTipe($request->tipe);
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        $soals = $query->paginate(20)->withQueryString();
        $kategoris = KategoriSoal::active()->get();

        return view('admin.soal.index', compact('soals', 'kategoris'));
    }

    public function create()
    {
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.create', compact('kategoris', 'tipes'));
    }

    public function getKepribadianCategories()
    {
        $kepribadianCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
        return response()->json($kepribadianCodes);
    }

    /**
     * Calculate bobot berdasarkan tipe soal
     */
    private function calculateBobot($tipe, $inputBobot, $kategoriId = null)
    {
        switch ($tipe) {
            case 'pg_pilih_2':
                // Untuk pilih 2, bobot = 0.5 untuk jawaban benar, 0 untuk salah
                return $inputBobot > 0 ? 0.5 : 0;
                
            case 'pg_bobot':
                // Untuk pg_bobot, gunakan bobot asli dari input (1-5 untuk kepribadian, 0-1 untuk lainnya)
                if ($kategoriId) {
                    $kategori = KategoriSoal::find($kategoriId);
                    if ($kategori) {
                        $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                        if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                            // Untuk kategori kepribadian, bobot 1-5
                            return max(1, min(5, $inputBobot));
                        }
                    }
                }
                // Untuk non-kepribadian, bobot 0-1
                return max(0, min(1, $inputBobot));
                
            case 'pg_satu':
            case 'gambar':
                // Untuk PG 1 jawaban dan gambar, bobot = 1 untuk benar, 0 untuk salah
                return $inputBobot > 0 ? 1 : 0;
                
            case 'benar_salah':
                // Untuk benar/salah, bobot = 1 untuk benar, 0 untuk salah
                return $inputBobot > 0 ? 1 : 0;
                
            default:
                return $inputBobot;
        }
    }

    public function store(Request $request)
    {
        // Debug logging
        \Log::info('=== STORE SOAL DEBUG ===');
        \Log::info('Request data: ', $request->all());
        \Log::info('Tipe soal: ' . $request->tipe);
        
        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:mudah,sedang,sulit',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:5'
        ];

        // Add image validation if tipe is gambar
        if ($request->tipe === 'gambar') {
            $rules['gambar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories (TKP/PSIKOTES via package mapping)
        // PERBAIKAN: Validasi bobot 1-5 hanya untuk tipe pg_bobot dengan kategori kepribadian
        if ($request->filled('kategori_id') && $request->tipe === 'pg_bobot') {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|integer|between:1,5';
                }
            }
        }

        try {
            $request->validate($rules);
            \Log::info('Validation passed for tipe: ' . $request->tipe);
        } catch (\Exception $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $e->getMessage());
        }

        try {
            DB::transaction(function () use ($request) {
                \Log::info('Starting transaction for tipe: ' . $request->tipe);
                
                $soalData = [
                    'pertanyaan' => $request->pertanyaan,
                    'tipe' => $request->tipe,
                    'level' => $request->level,
                    'kategori_id' => $request->kategori_id,
                    'pembahasan' => $request->pembahasan,
                    'pembahasan_type' => $request->pembahasan_type ?? 'text',
                    'jawaban_benar' => $request->jawaban_benar
                ];

                // Handle image upload
                if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                    $image = $request->file('gambar');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                    $soalData['gambar'] = $imagePath;
                }

                // Pembahasan image upload
                if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                    $img = $request->file('pembahasan_image');
                    $name = time() . '_' . $img->getClientOriginalName();
                    $path = $img->storeAs('pembahasan_images', $name, 'public');
                    $soalData['pembahasan_image'] = $path;
                }

                \Log::info('Creating soal with data: ', $soalData);
                $soal = Soal::create($soalData);
                \Log::info('Soal created with ID: ' . $soal->id);

                foreach ($request->opsi as $index => $opsiData) {
                    // Set bobot berdasarkan tipe soal
                    $bobot = $this->calculateBobot($request->tipe, $opsiData['bobot'] ?? 0, $request->kategori_id);
                    
                    $opsi = OpsiSoal::create([
                        'soal_id' => $soal->id,
                        'opsi' => chr(65 + $index), // A, B, C, D, E
                        'teks' => $opsiData['teks'],
                        'bobot' => $bobot
                    ]);
                    \Log::info('Created opsi with calculated bobot: ', $opsi->toArray());
                }
                
                \Log::info('Transaction completed successfully');
            });
            
            \Log::info('Redirecting with success message');
            return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            \Log::error('Database transaction failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    public function uploadWord(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:docx',
            ]);

            \Log::info("Upload Word dimulai...");

            $file = $request->file('file');
            $path = $file->getRealPath();

            \Log::info("File berhasil diupload", [
                'nama_file' => $file->getClientOriginalName(),
                'path' => $path,
            ]);

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
            \Log::info("File Word berhasil dibaca oleh PhpWord");

            $soals = $this->parseWordDocument($phpWord);
            \Log::info("Jumlah soal hasil parsing", ['count' => count($soals)]);

            DB::beginTransaction();

            foreach ($soals as $index => $soalData) {
                try {
                    \Log::info("Menyimpan soal ke-" . ($index + 1), $soalData);

                    $soal = Soal::create([
                        'kategori_id'   => $soalData['kategori_id'],
                        'tipe'          => $soalData['tipe'],
                        'level'         => $soalData['level'] ?? 'mudah',
                        'pertanyaan'    => $soalData['pertanyaan'],
                        'jawaban_benar' => $soalData['jawaban_benar'] ?? null,
                        'pembahasan'    => $soalData['pembahasan'] ?? null,
                    ]);

                    if (!empty($soalData['opsi']) && is_array($soalData['opsi'])) {
                        foreach ($soalData['opsi'] as $opsiData) {
                            \Log::info("Menyimpan opsi soal", $opsiData);

                            OpsiSoal::create([
                                'soal_id' => $soal->id,
                                'opsi'    => $opsiData['opsi'] ?? null, // misalnya A/B/C/D
                                'teks'    => $opsiData['teks'] ?? '',
                                'bobot'   => $opsiData['bobot'] ?? 0,
                            ]);
                        }
                    }

                    \Log::info("Soal ke-" . ($index + 1) . " berhasil disimpan.");
                } catch (\Exception $e) {
                    \Log::error("Gagal menyimpan soal ke-" . ($index + 1), [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e; // biar rollback
                }
            }

            DB::commit();

            return redirect()->route('admin.soal.index')
                ->with('success', 'Soal berhasil diupload dari file Word');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("UploadWord gagal total", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat upload Word. Cek log untuk detail.');
        }
    }



    /**
     * Parse Word Document ke array soal
     */
    private function parseWordDocument($phpWord)
    {
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        \Log::info("Raw text dari Word:", [$text]);

        $blocks = preg_split('/---/', $text);
        $soals = [];

        foreach ($blocks as $i => $block) {
            $lines = array_filter(array_map('trim', explode("\n", $block)));

            if (empty($lines)) {
                \Log::warning("Block soal ke-{$i} kosong, dilewati.");
                continue;
            }

            \Log::info("Lines block ke-{$i}:", $lines);

            // default
            $soalData = [
                'kategori_id'   => null,
                'tipe'          => null,
                'level'         => null,
                'pertanyaan'    => '',
                'opsi'          => [],
                'jawaban_benar' => null,
                'pembahasan'    => null,
            ];

            $mode = null;

            foreach ($lines as $line) {
                if (str_starts_with($line, '[kategori]')) {
                    $kode = trim(str_replace('[kategori]', '', $line));
                    \Log::info("Mencari kategori dengan kode: {$kode}");
                    $kategori = KategoriSoal::where('kode', $kode)->first();

                    if (!$kategori) {
                        \Log::error("Kategori tidak ditemukan", ['kode' => $kode]);
                        throw new \Exception("Kategori dengan kode '{$kode}' tidak ditemukan di database.");
                    }

                    $soalData['kategori_id'] = $kategori->id;
                } elseif (str_starts_with($line, '[tipe]')) {
                    $soalData['tipe'] = trim(str_replace('[tipe]', '', $line));
                } elseif (str_starts_with($line, '[level]')) {
                    $soalData['level'] = trim(str_replace('[level]', '', $line));
                } elseif (str_starts_with($line, 'Pertanyaan:')) {
                    $mode = 'pertanyaan';
                    continue;
                } elseif (str_starts_with($line, 'Opsi:')) {
                    $mode = 'opsi';
                    continue;
                } elseif (str_starts_with($line, 'Jawaban Benar:')) {
                    $soalData['jawaban_benar'] = trim(str_replace('Jawaban Benar:', '', $line));
                    $mode = null;
                    continue;
                } elseif (str_starts_with($line, 'Pembahasan:')) {
                    $mode = 'pembahasan';
                    continue;
                }

                // isi data sesuai mode
                if ($mode === 'pertanyaan') {
                    $soalData['pertanyaan'] .= ($soalData['pertanyaan'] ? "\n" : '') . $line;
                } elseif ($mode === 'opsi') {
                    // format umum: A. teks | bobot: x
                    if (preg_match('/^([A-Z])\.\s*(.*?)\s*\|\s*bobot:\s*(\d+)/i', $line, $m)) {
                        $opsiKey = $m[1];
                        $opsiText = $m[2];
                        $bobot = (int)$m[3];

                        $soalData['opsi'][] = [
                            'opsi'  => $opsiKey,
                            'teks'  => $opsiText,
                            'bobot' => $bobot,
                        ];
                    }
                } elseif ($mode === 'pembahasan') {
                    $soalData['pembahasan'] .= ($soalData['pembahasan'] ? "\n" : '') . $line;
                }
            }

            $soals[] = $soalData;
        }

        \Log::info("Hasil akhir parsing soal:", $soals);

        return $soals;
    }




    public function edit(Soal $soal)
    {
        $soal->load(['kategori', 'opsi']);
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.edit', compact('soal', 'kategoris', 'tipes'));
    }

    public function update(Request $request, Soal $soal)
    {
        // Debug logging
        \Log::info('=== UPDATE SOAL DEBUG ===');
        \Log::info('Soal ID: ' . $soal->id);
        \Log::info('Request data: ', $request->all());
        \Log::info('Opsi data: ', $request->opsi ?? []);

        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:mudah,sedang,sulit',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:1'
        ];

        // Add image validation if tipe is gambar and new image is uploaded
        if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
            $rules['gambar'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories
        // PERBAIKAN: Dynamic validation hanya untuk pg_bobot dengan kategori kepribadian
        if ($request->filled('kategori_id') && $request->tipe === 'pg_bobot') {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|numeric|between:1,5';
                }
            }
        }

        try {
            $request->validate($rules);
            \Log::info('Validation passed');
        } catch (\Exception $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            throw $e;
        }

        try {
            DB::transaction(function () use ($request, $soal) {
                \Log::info('Starting transaction...');

                $soalData = [
                    'pertanyaan' => $request->pertanyaan,
                    'tipe' => $request->tipe,
                    'level' => $request->level,
                    'kategori_id' => $request->kategori_id,
                    'pembahasan' => $request->pembahasan,
                    'pembahasan_type' => $request->pembahasan_type ?? $soal->pembahasan_type ?? 'text',
                    'jawaban_benar' => $request->jawaban_benar
                ];

                \Log::info('Soal data to update: ', $soalData);

                // Handle image upload
                if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                    if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
                        Storage::disk('public')->delete($soal->gambar);
                    }
                    $image = $request->file('gambar');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                    $soalData['gambar'] = $imagePath;
                } elseif ($request->tipe !== 'gambar' && $soal->gambar) {
                    if (Storage::disk('public')->exists($soal->gambar)) {
                        Storage::disk('public')->delete($soal->gambar);
                    }
                    $soalData['gambar'] = null;
                }

                // Handle pembahasan image
                if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                    if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                        Storage::disk('public')->delete($soal->pembahasan_image);
                    }
                    $img = $request->file('pembahasan_image');
                    $name = time() . '_' . $img->getClientOriginalName();
                    $path = $img->storeAs('pembahasan_images', $name, 'public');
                    $soalData['pembahasan_image'] = $path;
                } elseif ($request->pembahasan_type === 'text') {
                    if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                        Storage::disk('public')->delete($soal->pembahasan_image);
                    }
                    $soalData['pembahasan_image'] = null;
                }

                \Log::info('Updating soal with data: ', $soalData);
                $updated = $soal->update($soalData);
                \Log::info('Soal update result: ' . ($updated ? 'success' : 'failed'));

                // Delete existing options
                \Log::info('Deleting existing opsi...');
                $deletedCount = $soal->opsi()->delete();
                \Log::info('Deleted opsi count: ' . $deletedCount);

                // Create new options
                \Log::info('Creating new opsi...');
                foreach ($request->opsi as $index => $opsiData) {
                    // Set bobot berdasarkan tipe soal
                    $bobot = $this->calculateBobot($request->tipe, $opsiData['bobot'] ?? 0, $request->kategori_id);
                    
                    $newOpsi = OpsiSoal::create([
                        'soal_id' => $soal->id,
                        'opsi' => chr(65 + $index),
                        'teks' => $opsiData['teks'],
                        'bobot' => $bobot
                    ]);
                    \Log::info('Created opsi with calculated bobot: ', $newOpsi->toArray());
                }

                \Log::info('Transaction completed successfully');
            });

            \Log::info('Update completed, redirecting...');
            return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Update failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function show(Soal $soal)
    {
        return redirect()->route('admin.soal.index')->with('success', 'Redirected to soal list');
    }

    public function destroy(Soal $soal)
    {
        // Delete image if exists
        if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();
        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil dihapus');
    }
}
