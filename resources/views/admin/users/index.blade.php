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

            .status-select {
                padding: 4px 30px;
                border-radius: 16px;
                font-size: 14px;
                font-weight: 500;
                border: none;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                cursor: pointer;
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

            .status-select:focus {
                outline: none;
            }

            .status-wrapper {
                position: relative;
                display: inline-block;
            }

            .status-wrapper::after {
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
                                            <th class="text-center">PLAN</th>
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
                                                    @if ($user->hasActiveSubscription())
                                                        {{ $user->subscriptions ? json_decode($user->subscriptions->payment_details, true)['package'] ?? '-' : '-' }}
                                                    @else
                                                        -
                                                    @endif
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
                                                                    {{ $user->is_active ? 'selected' : '' }}>Active</option>
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

            // Update select background color when value changes
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    this.className = 'status-select ' + (this.value === '1' ? 'active' :
                        'inactive');
                });
            });
        });
    </script>
@endpush
