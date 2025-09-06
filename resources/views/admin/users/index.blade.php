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

            /* Search Box Styling */
            .input-group-addon {
                background-color: #f3f3f4;
                border-color: #e7eaec;
                color: #676a6c;
            }

            #searchInput {
                border-color: #e7eaec;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            #searchInput:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            #clearSearch {
                border-color: #e7eaec;
                color: #676a6c;
                transition: all 0.15s ease-in-out;
                border-left: none;
                border-radius: 0 3px 3px 0;
            }

            #clearSearch:hover {
                background-color: #f3f3f4;
                border-color: #d2d2d2;
                color: #333;
            }

            /* Ensure input border radius matches */
            #searchInput {
                border-radius: 3px 0 0 3px;
            }

            /* Page Length Control Styling */
            #pageLength {
                border-color: #e7eaec;
                border-radius: 3px;
                font-size: 12px;
                padding: 4px 8px;
                height: 30px;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            #pageLength:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            /* Align form group elements */
            .form-group label {
                display: block;
                margin-bottom: 5px;
            }

            /* Ensure all elements are aligned properly */
            .align-items-center {
                align-items: center !important;
            }

            .row.align-items-center {
                min-height: 40px;
            }

            /* Make search results vertically centered */
            #searchResults {
                line-height: 34px;
                display: inline-block;
            }

            /* Responsive styling for search controls */
            @media screen and (max-width: 767px) {
                .row.align-items-center {
                    flex-direction: column;
                    align-items: stretch !important;
                }

                .row.align-items-center>div {
                    margin-bottom: 10px;
                }

                .row.align-items-center>div:last-child {
                    margin-bottom: 0;
                }

                #searchInput {
                    border-radius: 3px;
                }

                #clearSearch {
                    border-radius: 0 3px 3px 0;
                }

                .d-flex.align-items-center.justify-content-center {
                    justify-content: flex-start !important;
                }

                #searchResults {
                    text-align: center;
                    line-height: normal;
                    margin-top: 5px;
                }
            }

            /* DataTable Custom Styling */
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #e7eaec;
                border-radius: 3px;
                padding: 4px 8px;
            }

            /* Responsive DataTable Styling */
            .dataTables_wrapper .dataTables_processing {
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid #e7eaec;
                border-radius: 3px;
                color: #676a6c;
            }

            /* Responsive breakpoints */
            @media screen and (max-width: 767px) {

                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_paginate {
                    text-align: center;
                    float: none;
                    margin-top: 10px;
                }

                .dataTables_wrapper .dataTables_paginate .paginate_button {
                    margin: 0 2px;
                }

                /* Hide less important columns on mobile */
                .dataTables-example th:nth-child(3),
                .dataTables-example td:nth-child(3) {
                    display: none;
                }
            }

            @media screen and (max-width: 480px) {

                /* Hide more columns on very small screens */
                .dataTables-example th:nth-child(4),
                .dataTables-example td:nth-child(4) {
                    display: none;
                }

                .dataTables-example th:nth-child(5),
                .dataTables-example td:nth-child(5) {
                    display: none;
                }
            }

            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #e7eaec;
                border-radius: 3px;
                padding: 6px 12px;
                margin-left: 8px;
            }

            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border: 1px solid #e7eaec;
                background: #fff;
                color: #676a6c !important;
                border-radius: 3px;
                margin: 0 2px;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #f3f3f4 !important;
                border-color: #d2d2d2;
                color: #333 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #1ab394 !important;
                border-color: #1ab394;
                color: #fff !important;
            }

            .dataTables_wrapper .dataTables_info {
                color: #676a6c;
                padding-top: 8px;
            }

            /* Responsive table wrapper */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Ensure table doesn't break on mobile */
            .table-responsive table {
                min-width: 600px;
            }

            @media screen and (max-width: 767px) {
                .table-responsive table {
                    min-width: 100%;
                }
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
                            <!-- Search Box and Controls -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-search"></i>
                                        </span>
                                        <input type="text" id="searchInput" class="form-control"
                                            placeholder="Cari berdasarkan nama atau email...">
                                        <span class="input-group-btn">
                                            <button type="button" id="clearSearch" class="btn btn-default"
                                                style="display: none;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span style="font-size: 12px; color: #676a6c; margin-right: 8px;">Tampilkan</span>
                                        <select id="pageLength" class="form-control input-sm"
                                            style="width: 60px; display: inline-block; margin: 0 5px;">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <span style="font-size: 12px; color: #676a6c; margin-left: 5px;">data</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <span id="searchResults" class="text-muted" style="font-size: 12px;"></span>
                                </div>
                            </div>

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
                                                        @canBeImpersonated($user)
                                                        <a href="#" class="action-icon impersonate-btn"
                                                            onclick="confirmImpersonate({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                                            title="Login sebagai user ini">
                                                            <i class="fa fa-user-secret" style="color: #28a745;"></i>
                                                        </a>
                                                        @endCanBeImpersonated
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
            // Wait a bit to ensure DOM is fully ready
            setTimeout(function() {
                window.userTable = $('.dataTables-example').DataTable({
                    pageLength: 25,
                    responsive: true,
                    dom: 'rt<"bottom"ip><"clear">',
                    searching: true, // Enable searching functionality
                    buttons: [],
                    initComplete: function() {
                        // DataTable initialized successfully
                    },
                    language: {
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        search: "Cari:",
                        searchPlaceholder: "Cari nama atau email...",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Tidak ada data yang tersedia",
                        zeroRecords: "Tidak ada data yang cocok dengan pencarian"
                    },
                    order: [
                        [0, 'asc']
                    ], // Sort by name column ascending
                    columnDefs: [{
                            targets: [0, 1], // Name and Email columns
                            searchable: true
                        },
                        {
                            targets: [2, 3, 4], // Plan, Status, Actions columns
                            searchable: false
                        }
                    ]
                });

                // Custom search functionality - using event delegation
                $(document).on('keyup input paste', '#searchInput', function() {
                    var searchValue = this.value;
                    if (window.userTable && typeof window.userTable.search === 'function') {
                        window.userTable.search(searchValue).draw();
                        updateSearchResults();
                        toggleClearButton(searchValue);
                    }
                });

                // Clear search button - using event delegation
                $(document).on('click', '#clearSearch', function() {
                    $('#searchInput').val('').focus();
                    if (window.userTable) {
                        window.userTable.search('').draw();
                        updateSearchResults();
                        toggleClearButton('');
                    }
                });

                // Function to toggle clear button visibility
                function toggleClearButton(searchValue) {
                    if (searchValue.length > 0) {
                        $('#clearSearch').show();
                    } else {
                        $('#clearSearch').hide();
                    }
                }

                // Function to update search results count
                function updateSearchResults() {
                    var searchValue = $('#searchInput').val();
                    var totalRecords = window.userTable.page.info().recordsTotal;
                    var filteredRecords = window.userTable.page.info().recordsDisplay;

                    if (searchValue) {
                        $('#searchResults').html(
                            '<i class="fa fa-info-circle"></i> ' +
                            filteredRecords + ' dari ' + totalRecords + ' data ditemukan'
                        );
                    } else {
                        $('#searchResults').html('');
                    }
                }

                // Initial call to set up search results
                updateSearchResults();

                // Initialize clear button state
                toggleClearButton($('#searchInput').val());

                // Page length control - using event delegation
                $(document).on('change', '#pageLength', function() {
                    if (window.userTable) {
                        window.userTable.page.len($(this).val()).draw();
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

            }, 100); // 100ms delay to ensure DOM is ready
        });

        // Impersonate confirmation function
        function confirmImpersonate(userId, userName, userEmail) {
            Swal.fire({
                title: '<i class="fa fa-user-secret" style="color: #28a745;"></i> Konfirmasi Impersonate',
                html: `
                    <div class="text-left">
                        <p><strong>Anda akan login sebagai user berikut:</strong></p>
                        <div class="user-info p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <p><strong>Nama:</strong> ${userName}</p>
                            <p><strong>Email:</strong> ${userEmail}</p>
                        </div>
                        <p class="text-warning"><i class="fa fa-exclamation-triangle"></i>
                        <strong>Peringatan:</strong> Semua aktivitas akan tercatat sebagai user ini.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-sign-in"></i> Ya, Login Sebagai User Ini',
                cancelButtonText: '<i class="fa fa-times"></i> Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang login sebagai user lain...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to impersonate route
                    window.location.href = "{{ route('admin.impersonate', ':userId') }}".replace(':userId',
                    userId);
                }
            });
        }
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
