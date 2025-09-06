@extends('layouts.app')

@section('content')

    @push('styles')
        <style>
            .avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: white;
            }

            .status-select,
            .package-select {
                padding: 4px 30px;
                border-radius: 16px;
                font-size: 14px;
                font-weight: 500;
                border: none;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                cursor: pointer;
                min-width: 120px;
            }

            .status-select option[value="1"],
            .status-select.active {
                background-color: #ecfdf3;
                color: #027a48;
            }

            .status-select option[value="0"],
            .status-select.inactive {
                background-color: #f2f4f7;
                color: #344054;
            }

            /* Package select styling */
            .package-select option[value="kecermatan"],
            .package-select.kecermatan {
                background-color: #eff6ff;
                color: #1d4ed8;
            }

            .package-select option[value="psikologi"],
            .package-select.psikologi {
                background-color: #fdf4ff;
                color: #a21caf;
            }

            .package-select option[value="lengkap"],
            .package-select.lengkap {
                background-color: #f0fdf4;
                color: #15803d;
            }

            .package-select option[value=""],
            .package-select.no-package {
                background-color: #f9fafb;
                color: #6b7280;
            }

            .action-icon {
                color: #667085;
                margin: 0 4px;
                cursor: pointer;
            }

            .fa-trash {
                color: #f13535;
                font-size: 20px;
            }

            .fa-edit {
                color: #007BFF;
                font-size: 18px;
            }

            .fa-info-circle {
                color: #000;
                font-size: 18px;
            }

            .username {
                font-weight: 500;
                color: #101828;
            }

            .handle {
                color: #667085;
                font-size: 14px;
            }

            .status-select:focus,
            .package-select:focus {
                outline: none;
            }

            .status-wrapper,
            .package-wrapper {
                position: relative;
                display: inline-block;
            }

            .status-wrapper::after,
            .package-wrapper::after {
                content: '';
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                width: 0;
                height: 0;
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-top: 5px solid currentColor;
                pointer-events: none;
            }
        </style>
    @endpush

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Data user</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>USER</th>
                                            <th>EMAIL</th>
                                            <th class="text-center">PACKAGE</th>
                                            <th class="text-center">STATUS</th>
                                            <th class="text-center">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][array_rand(['primary', 'success', 'info', 'warning', 'danger'])] }} mr-3">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div class="username">{{ $user->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td class="text-center">
                                                    <div class="package-wrapper">
                                                        <form method="POST"
                                                            action="{{ route('admin.users.updatePackage', $user->id) }}"
                                                            class="m-0">
                                                            @csrf
                                                            @method('PUT')
                                                            @php
                                                                $currentPackage = $user->package ?? '';
                                                            @endphp
                                                            <select name="package" onchange="this.form.submit()"
                                                                class="package-select {{ $currentPackage ? strtolower($currentPackage) : 'no-package' }}">
                                                                <option value=""
                                                                    {{ !$currentPackage ? 'selected' : '' }}>No Package
                                                                </option>
                                                                <option value="kecermatan"
                                                                    {{ $currentPackage === 'kecermatan' ? 'selected' : '' }}>
                                                                    Kecermatan</option>
                                                                <option value="psikologi"
                                                                    {{ $currentPackage === 'psikologi' ? 'selected' : '' }}>
                                                                    Psikologi</option>
                                                                <option value="lengkap"
                                                                    {{ $currentPackage === 'lengkap' ? 'selected' : '' }}>
                                                                    Lengkap</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="status-wrapper">
                                                        <form method="POST"
                                                            action="{{ route('admin.users.update', $user->id) }}"
                                                            class="m-0">
                                                            @csrf
                                                            @method('PUT')
                                                            <select name="is_active" onchange="this.form.submit()"
                                                                class="status-select {{ $user->is_active ? 'active' : 'inactive' }}">
                                                                <option value="1"
                                                                    {{ $user->is_active ? 'selected' : '' }}>Active
                                                                </option>
                                                                <option value="0"
                                                                    {{ !$user->is_active ? 'selected' : '' }}>Inactive
                                                                </option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <a href="{{ route('user.profile', $user->id) }}"
                                                            class="action-icon">
                                                            <i class="fa fa-info-circle"></i>
                                                        </a>
                                                        @if ($user->canBeImpersonated())
                                                            <a href="#" class="action-icon impersonate-btn"
                                                                data-user-id="{{ $user->id }}"
                                                                data-user-name="{{ $user->name }}"
                                                                data-user-email="{{ $user->email }}"
                                                                title="Login sebagai user ini">
                                                                <i class="fa fa-user-secret" style="color: #28a745;"></i>
                                                            </a>
                                                        @endif
                                                        <form action="{{ route('admin.users.delete', $user->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                style="border: none; background: none; padding: 0;">
                                                                <i class="fa fa-trash action-icon"
                                                                    style="color: red; cursor: pointer;"></i>
                                                            </button>
                                                        </form>
                                                        <a href="#" class="action-icon">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: 'tp<"bottom"l>',
                searching: true,
                buttons: [],
                language: {
                    lengthMenu: "Show _MENU_ entries"
                }
            });

            // Update select background color when value changes for status
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    this.className = 'status-select ' + (this.value === '1' ? 'active' :
                        'inactive');
                });
            });

            // Update select background color when value changes for package
            document.querySelectorAll('.package-select').forEach(select => {
                select.addEventListener('change', function() {
                    let packageClass = '';
                    switch (this.value) {
                        case 'kecermatan':
                            packageClass = 'kecermatan';
                            break;
                        case 'psikologi':
                            packageClass = 'psikologi';
                            break;
                        case 'lengkap':
                            packageClass = 'lengkap';
                            break;
                        default:
                            packageClass = 'no-package';
                    }
                    this.className = 'package-select ' + packageClass;
                });
            });

            // Impersonate button handler
            $('.impersonate-btn').on('click', function(e) {
                e.preventDefault();

                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');
                const userEmail = $(this).data('user-email');

                Swal.fire({
                    title: 'Konfirmasi Impersonate',
                    html: `
                        <div class="text-left">
                            <p><strong>Anda akan login sebagai:</strong></p>
                            <div class="user-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                                <p><i class="fa fa-user"></i> <strong>Nama:</strong> ${userName}</p>
                                <p><i class="fa fa-envelope"></i> <strong>Email:</strong> ${userEmail}</p>
                                <p><i class="fa fa-id-badge"></i> <strong>ID:</strong> ${userId}</p>
                            </div>
                            <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> <strong>Peringatan:</strong></p>
                            <ul class="text-left" style="margin-left: 20px;">
                                <li>Semua aktivitas akan tercatat dalam log</li>
                                <li>Gunakan fitur ini hanya untuk troubleshooting</li>
                                <li>Klik "Stop Impersonating" untuk kembali ke akun admin</li>
                            </ul>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-user-secret"></i> Ya, Impersonate',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide',
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            // Show loading state
                            Swal.showLoading();

                            // Redirect to impersonate route using lab404 package
                            window.location.href =
                                `{{ route('admin.impersonate', ':userId') }}`.replace(
                                    ':userId', userId);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });
        });
    </script>

    <style>
        .swal-wide {
            width: 500px !important;
        }

        .user-info {
            border-left: 4px solid #28a745;
        }

        .impersonate-btn:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        .swal2-popup {
            font-size: 14px;
        }

        .swal2-confirm {
            margin-right: 10px;
        }
    </style>
@endpush
