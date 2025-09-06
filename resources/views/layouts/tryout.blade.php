<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Tryout')</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    
    <style>
        /* Tryout Fullscreen Styles */
        body.tryout-fullscreen {
            overflow: hidden;
        }
        
        body.tryout-fullscreen #wrapper {
            margin-left: 0 !important;
        }
        
        body.tryout-fullscreen #page-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        
        body.tryout-fullscreen .navbar-static-side {
            display: none !important;
        }
        
        body.tryout-fullscreen .navbar-top {
            display: none !important;
        }
        
        body.tryout-fullscreen .footer {
            display: none !important;
        }
        
        /* Fullscreen Toggle Button */
        .fullscreen-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fullscreen-toggle:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }
        
        /* Exit Fullscreen Button */
        .exit-fullscreen {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none;
        }
        
        .exit-fullscreen:hover {
            background: #c82333;
            transform: scale(1.05);
        }
        
        /* Fullscreen Content */
        .tryout-fullscreen .tryout-content {
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        
        /* Focus Mode Indicator */
        .focus-mode-indicator {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 123, 255, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            z-index: 9997;
            display: none;
            animation: pulse 2s infinite;
        }
        
        body.tryout-fullscreen .focus-mode-indicator {
            display: block;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        /* Countdown Modal Styles */
        .countdown-display {
            margin: 20px 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .countdown-display #countdownNumber {
            font-size: 4rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: countdownPulse 1s ease-in-out;
        }
        
        @keyframes countdownPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
        }
        
        .modal-footer {
            border-radius: 0 0 15px 15px;
        }
        
        /* Hide elements in fullscreen */
        body.tryout-fullscreen .fullscreen-toggle {
            display: none;
        }
        
        body.tryout-fullscreen .exit-fullscreen {
            display: block;
        }
        
                 /* Impersonate banner in fullscreen */
         body.tryout-fullscreen .impersonate-banner {
             position: fixed;
             top: 0;
             left: 0;
             right: 0;
             z-index: 9998;
         }
         
         body.tryout-fullscreen .tryout-content {
             margin-top: 60px; /* Space for impersonate banner */
         }
         
         /* Global Impersonate Styling */
         .impersonate-banner {
             border-left: 4px solid #ffc107 !important;
             background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
             margin: 0;
             border-radius: 0;
             border-left: none;
             border-right: none;
         }
         
         .stop-impersonate-btn {
             transition: all 0.3s ease;
             border-radius: 6px;
             font-weight: 500;
             border: 2px solid #dc3545;
             color: #dc3545;
             background: transparent;
         }
         
         .stop-impersonate-btn:hover {
             transform: translateY(-1px);
             box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
             background: #dc3545;
             color: white;
         }
         
         .stop-impersonate-btn i {
             margin-right: 5px;
         }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .fullscreen-toggle,
            .exit-fullscreen {
                top: 10px;
                right: 10px;
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .exit-fullscreen {
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
    
    @stack('styles')
</head>

<body class="tryout-mode">
    <!-- Fullscreen Toggle Button -->
    <button class="fullscreen-toggle" id="fullscreenToggle" title="Masuk Mode Fullscreen">
        <i class="fa fa-expand"></i>
    </button>
    
    <!-- Exit Fullscreen Button -->
    <button class="exit-fullscreen" id="exitFullscreen" title="Keluar dari Mode Fullscreen">
        <i class="fa fa-compress"></i> Keluar Fullscreen
    </button>
    
    <!-- Focus Mode Indicator -->
    <div class="focus-mode-indicator" id="focusModeIndicator">
        <i class="fa fa-eye"></i> Mode Fokus Aktif
    </div>
    
    <!-- Auto Fullscreen Countdown Modal -->
    <div class="modal fade" id="autoFullscreenModal" tabindex="-1" role="dialog" aria-labelledby="autoFullscreenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="autoFullscreenModalLabel">
                        <i class="fa fa-expand"></i> Mode Fokus Otomatis
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fa fa-clock-o fa-3x text-primary mb-3"></i>
                        <h4>Tryout akan dimulai dalam:</h4>
                        <div class="countdown-display">
                            <span id="countdownNumber" class="display-1 text-primary font-weight-bold">3</span>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Mode Fokus</strong> akan otomatis aktif untuk mengurangi distraksi dan membantu Anda fokus mengerjakan soal.
                    </div>
                    <div class="text-muted">
                        <small>
                            <i class="fa fa-keyboard-o"></i> Tekan <kbd>F11</kbd> untuk keluar dari mode fokus<br>
                            <i class="fa fa-mouse-pointer"></i> Klik nomor soal untuk navigasi
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAutoFullscreen">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="startTryoutNow">
                        <i class="fa fa-play"></i> Mulai Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="wrapper">
        @include('components.sidenav')
        <div id="page-wrapper" class="gray-bg">
            @include('components.topnav')
            
            @if (session('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Impersonate Warning Banner --}}
            @if(auth()->user() && app('impersonate')->isImpersonating())
                <div class="alert alert-warning alert-dismissible fade show impersonate-banner" role="alert" style="margin: 0; border-radius: 0; border-left: none; border-right: none;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fa fa-user-secret"></i>
                                <strong>Mode Impersonate Aktif!</strong> 
                                Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
                                @if(app('impersonate')->getImpersonator())
                                    (Admin: {{ app('impersonate')->getImpersonator()->name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <button onclick="confirmStopImpersonate()" class="btn btn-sm btn-outline-danger stop-impersonate-btn">
                                    <i class="fa fa-stop"></i> Stop Impersonate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tryout Content Container -->
            <div class="tryout-content">
                @yield('content')
            </div>
            
            @include('components.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.appRoutes = {
            simpanHasil: "{{ route('kecermatan.simpanHasil') }}",
        };
        
        // Stop impersonate confirmation function
        function confirmStopImpersonate() {
            Swal.fire({
                title: '<i class="fa fa-stop" style="color: #dc3545;"></i> Stop Impersonate',
                html: `
                    <div class="text-left">
                        <p><strong>Anda akan keluar dari mode impersonate dan kembali ke dashboard admin.</strong></p>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Info:</strong> Semua perubahan yang dilakukan akan tetap tersimpan.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-stop"></i> Ya, Stop Impersonate',
                cancelButtonText: '<i class="fa fa-times"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang keluar dari mode impersonate...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Redirect to leave impersonation route
                    window.location.href = "{{ route('leave.impersonation') }}";
                }
            });
        }

    </script>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/generateSoal.js') }}"></script>
    <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/inspinia.js') }}"></script>
    
    <!-- Fullscreen Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fullscreenToggle = document.getElementById('fullscreenToggle');
            const exitFullscreen = document.getElementById('exitFullscreen');
            const body = document.body;
            
            // Check if fullscreen mode is stored in sessionStorage
            if (sessionStorage.getItem('tryoutFullscreen') === 'true') {
                body.classList.add('tryout-fullscreen');
            }
            
            // Auto Fullscreen with Countdown
            const autoFullscreenModal = document.getElementById('autoFullscreenModal');
            const countdownNumber = document.getElementById('countdownNumber');
            const cancelAutoFullscreen = document.getElementById('cancelAutoFullscreen');
            const startTryoutNow = document.getElementById('startTryoutNow');
            
            // Check if auto fullscreen is needed
            const autoFullscreenTryoutId = '{{ session("auto_fullscreen_tryout") }}';
            if (autoFullscreenTryoutId && !sessionStorage.getItem('autoFullscreenShown')) {
                // Show modal after page load
                setTimeout(() => {
                    if (autoFullscreenModal) {
                        $(autoFullscreenModal).modal('show');
                    }
                }, 500);
            }
            
            // Countdown function
            function startCountdown() {
                let count = 3;
                countdownNumber.textContent = count;
                
                const countdownInterval = setInterval(() => {
                    count--;
                    countdownNumber.textContent = count;
                    
                    // Add pulse animation
                    countdownNumber.style.animation = 'none';
                    setTimeout(() => {
                        countdownNumber.style.animation = 'countdownPulse 1s ease-in-out';
                    }, 10);
                    
                    if (count <= 0) {
                        clearInterval(countdownInterval);
                        $(autoFullscreenModal).modal('hide');
                        
                        // Activate fullscreen mode
                        setTimeout(() => {
                            body.classList.add('tryout-fullscreen');
                            sessionStorage.setItem('tryoutFullscreen', 'true');
                            sessionStorage.setItem('autoFullscreenShown', 'true');
                            
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Mode Fokus Aktif!',
                                    text: 'Selamat mengerjakan tryout. Klik nomor soal untuk navigasi.',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        }, 300);
                    }
                }, 1000);
            }
            
            // Event listeners for modal buttons
            if (startTryoutNow) {
                startTryoutNow.addEventListener('click', function() {
                    startCountdown();
                });
            }
            
            if (cancelAutoFullscreen) {
                cancelAutoFullscreen.addEventListener('click', function() {
                    $(autoFullscreenModal).modal('hide');
                    sessionStorage.setItem('autoFullscreenShown', 'true');
                    
                    // Clear session flag
                    fetch('{{ route("user.tryout.index.post") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            clear_auto_fullscreen: true
                        })
                    });
                });
            }
            
            // Show welcome message for first-time users (only if not auto fullscreen)
            if (!sessionStorage.getItem('tryoutWelcomeShown') && !autoFullscreenTryoutId) {
                setTimeout(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Selamat Datang di Mode Tryout!',
                            html: `
                                <div class="text-left">
                                    <p><strong>Tips untuk pengalaman optimal:</strong></p>
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-expand text-primary"></i> Gunakan <strong>Mode Fokus</strong> untuk mengurangi distraksi</li>
                                        <li><i class="fa fa-keyboard-o text-info"></i> Tekan <kbd>F11</kbd> untuk toggle mode fokus</li>
                                        <li><i class="fa fa-mouse-pointer text-success"></i> Klik nomor soal untuk navigasi</li>
                                        <li><i class="fa fa-save text-warning"></i> Tekan <kbd>Ctrl+S</kbd> untuk simpan jawaban</li>
                                    </ul>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6',
                            showCloseButton: true
                        });
                    }
                    sessionStorage.setItem('tryoutWelcomeShown', 'true');
                }, 1000);
            }
            
            // Toggle fullscreen mode
            fullscreenToggle.addEventListener('click', function() {
                body.classList.add('tryout-fullscreen');
                sessionStorage.setItem('tryoutFullscreen', 'true');
                
                // Show confirmation message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Mode Fokus Aktif',
                        text: 'Anda sekarang dalam mode fokus. Sidebar dan menu telah disembunyikan untuk mengurangi distraksi. Tekan F11 atau klik tombol "Keluar Fullscreen" untuk kembali.',
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
            
            // Exit fullscreen mode
            exitFullscreen.addEventListener('click', function() {
                // Show confirmation dialog
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Keluar Mode Fokus?',
                        text: 'Apakah Anda yakin ingin keluar dari mode fokus? Sidebar dan menu akan ditampilkan kembali.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Tetap Fokus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            body.classList.remove('tryout-fullscreen');
                            sessionStorage.removeItem('tryoutFullscreen');
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Mode Normal',
                                text: 'Anda telah keluar dari mode fokus. Sidebar dan menu telah dikembalikan.',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                } else {
                    // Fallback if SweetAlert is not available
                    body.classList.remove('tryout-fullscreen');
                    sessionStorage.removeItem('tryoutFullscreen');
                }
            });
            
            // Simplified Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Fullscreen toggle (F11)
                if (e.key === 'F11') {
                    e.preventDefault();
                    if (body.classList.contains('tryout-fullscreen')) {
                        exitFullscreen.click();
                    } else {
                        fullscreenToggle.click();
                    }
                }
                
                // Escape key to exit fullscreen
                if (e.key === 'Escape' && body.classList.contains('tryout-fullscreen')) {
                    exitFullscreen.click();
                }
                
                // Ctrl+S to save answer (only when not in input fields)
                if (e.ctrlKey && e.key === 's' && !e.target.matches('input, textarea, select')) {
                    e.preventDefault();
                    const saveBtn = document.querySelector('button[type="submit"]');
                    if (saveBtn) saveBtn.click();
                }
            });
            
            // Warn user when trying to leave page in focus mode
            window.addEventListener('beforeunload', function(e) {
                if (body.classList.contains('tryout-fullscreen')) {
                    e.preventDefault();
                    e.returnValue = 'Anda sedang dalam mode fokus. Pastikan jawaban sudah disimpan sebelum meninggalkan halaman.';
                    return e.returnValue;
                }
            });
            
            // Handle page visibility change (tab switching)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden && body.classList.contains('tryout-fullscreen')) {
                    // User switched to another tab
                    console.log('User switched tab while in focus mode');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>

</html> 