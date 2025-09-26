@extends('layouts.app')

@push('styles')
    <style>
        .soal-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .soal-row {
            margin-bottom: 20px;
        }

        .soal-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .soal-input {
            flex: 1;
            margin-right: 10px;
        }

        .type-selector {
            margin-bottom: 30px;
        }

        .btn-karakter {
            background-color: #1ab394;
            color: white;
            min-width: 100px;
            border: none;
            transition: all 0.2s ease;
            padding: 8px 6px;
            border-radius: 8px;
            font-size: 15px;
            margin-top: 27px;
        }

        .btn-karakter:hover {
            background-color: #18a689;
            color: white;
            transform: translateY(-1px);
        }

        .btn-karakter:active {
            transform: translateY(1px);
        }

        /* Style untuk input yang sudah diisi */
        .form-control:not(:placeholder-shown) {
            font-weight: 600;
            color: #2c3e50;
            background-color: #f8f9fa;
            letter-spacing: 1px;
            font-size: 1.1em;
        }

        .soal-label {
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .action-buttons {
            margin-top: 30px;
        }

        .btn-isi-otomatis {
            background-color: #0d6efd;
            color: white;
            padding: 8px 6px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-isi-otomatis:hover {
            background-color: #0b5ed7;
            color: white;
            transform: translateY(-1px);
        }

        .btn-mulai-tes {
            background-color: #1ab394;
            color: white;
            padding: 8px 6px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-mulai-tes:hover {
            background-color: #18a689;
            color: white;
            transform: translateY(-1px);
        }

        .instructions {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 0.375rem !important;
            height: 38px !important;
        }

        .form-control:focus {
            border-color: #4a4de7;
            box-shadow: 0 0 0 0.2rem rgba(74, 77, 231, 0.25);
        }

        .form-select:focus {
            border-color: #4a4de7;
            box-shadow: 0 0 0 0.2rem rgba(74, 77, 231, 0.25);
        }

        .btn-light {
            padding: 8px 0px;
            border-radius: 8px;
            font-size: 15px;
            width: 120px;
        }

        .dropdown-item {
            padding-block: 10px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .soal-container {
                padding: 15px;
            }

            .btn-karakter {
                min-width: 80px;
                font-size: 14px;
            }

            .soal-group {
                flex-direction: column;
                align-items: stretch;
            }

            .soal-input {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush


@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated-fadeInRight">
            <div class="ibox">
                <div class="ibox-title">
                    <h2 class="text-dark font-weight-bold mb-3">Tes Kecermatan</h2>
                </div>
                <div class="ibox-content">
                    <form id="kecermatanForm" action="{{ route('kecermatan.soal') }}" method="GET">
                        <p class="instructions">Inputkan Huruf, Angka, atau Simbol dengan maksimal 5 karakter (karakter dapat
                            digabung)
                        </p>

                        <div class="form-group d-flex align-items-center">
                            <div class="dropdown mr-2">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Pilih Jenis
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" data-value="huruf">Huruf</a>
                                    <a class="dropdown-item" href="#" data-value="angka">Angka</a>
                                    <a class="dropdown-item" href="#" data-value="simbol">Simbol</a>
                                    <a class="dropdown-item" href="#" data-value="acak">Acak</a>
                                </div>
                            </div>
                            <button type="button" id="isiOtomatisBtn" class="btn btn-isi-otomatis mr-2">
                                Isi Otomatis
                            </button>
                            <button type="submit" class="btn btn-mulai-tes">
                                Mulai Tes
                            </button>
                        </div>
                        <!-- Layout soal sama seperti sebelumnya -->
                        <!-- Baris 1 (Soal 1-3) -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 1</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="0" placeholder=").%<-" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="0">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 2</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="1" placeholder="&'!_" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="1">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 3</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="2" placeholder="+>_)@" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="2">Huruf</button>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 (Soal 4-6) -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 4</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="3" placeholder="{@|>*" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="3">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 5</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="4" placeholder="/^<{}" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="4">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 6</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="5" placeholder=":^%|&" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="5">Huruf</button>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 3 (Soal 7-9) -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 7</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="6" placeholder="(.@|-" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="6">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 8</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="7" placeholder="[)_+#" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="7">Huruf</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 9</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="8" placeholder="?^{*" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="8">Huruf</button>
                                </div>
                            </div>
                        </div>

                        <!-- Soal 10 -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="soal-group">
                                    <div class="soal-input">
                                        <label class="soal-label">Kolom / Soal 10</label>
                                        <input type="text" name="questions[]" class="form-control karakter-input"
                                            data-index="9" placeholder="'*=?[" maxlength="5">
                                    </div>
                                    <button type="button" class="btn btn-karakter karakter-btn"
                                        data-index="9">Huruf</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let selectedJenis = 'huruf';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const inputs = Array.from(document.querySelectorAll('input[name="questions[]"]'));
            const dropdownBtn = document.getElementById('dropdownMenuButton');
            const dropdownMenu = document.querySelector('.dropdown-menu[aria-labelledby="dropdownMenuButton"]');
            const dropdownItems = Array.from(document.querySelectorAll('.dropdown-item'));
            const isiOtomatisBtn = document.getElementById('isiOtomatisBtn');
            const form = document.getElementById('kecermatanForm');

            const CHAR_SETS = {
                huruf: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                angka: '0123456789',
                simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?'
            };

            function generateRandomStringFrom(set, length = 5) {
                let result = '';
                const n = set.length;
                for (let i = 0; i < length; i++) {
                    result += set[Math.floor(Math.random() * n)];
                }
                return result;
            }

            function fillInputs(values) {
                inputs.forEach((input, idx) => {
                    input.value = values[idx] || '';
                });
            }

            // Dropdown: open/close fallback (avoid Bootstrap JS conflicts)
            if (dropdownBtn && dropdownMenu) {
                dropdownBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    dropdownMenu.classList.toggle('show');
                });
                document.addEventListener('click', (e) => {
                    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }

            // Dropdown: pilih jenis
            dropdownItems.forEach((item) => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const value = item.getAttribute('data-value');
                    selectedJenis = value;
                    dropdownBtn.textContent = 'Jenis: ' + item.textContent.trim();
                    if (dropdownMenu) dropdownMenu.classList.remove('show');
                });
            });

            // Isi otomatis via backend agar konsisten dengan aturan server
            isiOtomatisBtn.addEventListener('click', async () => {
                try {
                    const resp = await fetch("{{ route('kecermatan.generateKarakter') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ jenis: selectedJenis })
                    });
                    const data = await resp.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal menghasilkan karakter');
                    }
                    const hasil = data.data || [];
                    if (hasil.length !== 10) {
                        throw new Error('Server mengembalikan jumlah set tidak sesuai');
                    }
                    fillInputs(hasil);
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: err.message || 'Terjadi kesalahan saat isi otomatis'
                    });
                }
            });

            // Tombol per kolom (karakter-btn) untuk cepat mengisi 5 karakter
            document.querySelectorAll('.karakter-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const idx = Number(btn.getAttribute('data-index'));
                    if (Number.isNaN(idx)) return;

                    // Tentukan jenis dari label tombol (Huruf/Angka/Simbol/Acak)
                    const label = (btn.textContent || '').trim().toLowerCase();
                    let setToUse = '';
                    if (label === 'huruf') setToUse = CHAR_SETS.huruf;
                    else if (label === 'angka') setToUse = CHAR_SETS.angka;
                    else if (label === 'simbol') setToUse = CHAR_SETS.simbol;
                    else { // acak
                        setToUse = CHAR_SETS.huruf + CHAR_SETS.angka + CHAR_SETS.simbol;
                    }
                    inputs[idx].value = generateRandomStringFrom(setToUse, 5);

                    // Siklus label tombol Huruf -> Angka -> Simbol -> Acak -> Huruf
                    const labels = ['Huruf', 'Angka', 'Simbol', 'Acak'];
                    const currentIdx = labels.findIndex(l => l.toLowerCase() === (btn.textContent || '').trim().toLowerCase());
                    const nextLabel = labels[(currentIdx + 1) % labels.length];
                    btn.textContent = nextLabel;
                });
            });

            // Validasi sebelum submit: pastikan 10 input terisi dan max 5 karakter
            form.addEventListener('submit', (e) => {
                const values = inputs.map(i => (i.value || '').trim());
                const isComplete = values.length === 10 && values.every(v => v.length > 0 && v.length <= 5);
                if (!isComplete) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Pastikan semua 10 kolom terisi maksimal 5 karakter.'
                    });
                }
            });
        });
    </script>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Load class KecermatanSoal -->
    <script src="{{ asset('js/kecermatanSoal.js') }}"></script>

    <!-- Inisialisasi game -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const config = {
                routes: {
                    nextSoal: "{{ route('kecermatan.nextSoal') }}",
                    simpanHasil: "{{ route('kecermatan.simpanHasil') }}"
                },
                userId: "{{ auth()->id() }}",
                csrfToken: document.querySelector('meta[name="csrf-token"]').content
            };

            const game = new KecermatanSoal(config);
            game.init();
        });
    </script>
@endpush
