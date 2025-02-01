@extends('layouts.app')

@section('title', 'User Profile')

@section('page-title', 'Profil User')

@push('breadcrumbs')
    <li class="breadcrumb-item active">
        <strong>Profil User</strong>
    </li>
@endpush

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary mb-3 text-white"
                            style="width: 80px; height: 80px;">
                            <span class="h3">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>

                        <!-- Free Programs -->
                        <div class="list-group mb-4 text-left">
                            <a href="#" class="list-group-item list-group-item-action">
                                Kepribadian <i class="fa fa-chevron-right float-right"></i>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                Kecerdasan <i class="fa fa-chevron-right float-right"></i>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                Kecermatan <i class="fa fa-chevron-right float-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Column -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">STATUS BERLANGGANAN</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-4">
                                    <p class="mb-1"><strong>Status:</strong></p>
                                    <p class="mb-0">{{ $user->hasActiveSubscription() ? 'Aktif' : 'Tidak Aktif' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="mb-1"><strong>Masa Aktif Langganan:</strong></p>
                                    <p class="mb-0">
                                        {{ $subscription ? date('l, d F Y', strtotime($subscription->end_date)) : 'Tidak Ada' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-4">
                                    <p class="mb-1"><strong>Tipe Pembayaran:</strong></p>
                                    <p class="mb-0">{{ $subscription ? $subscription->payment_method : 'Tidak Ada' }}</p>
                                </div>
                                <div>
                                    <p class="mb-1"><strong>Bank / Metode:</strong></p>
                                    @if ($subscription && $subscription->payment_details)
                                        @php
                                            $paymentDetails = json_decode($subscription->payment_details, true);
                                        @endphp

                                        <p class="mb-0">
                                            @if (isset($paymentDetails['bank']))
                                                {{ strtoupper($paymentDetails['bank']) }}
                                            @elseif(isset($paymentDetails['e_wallet']))
                                                {{ strtoupper($paymentDetails['e_wallet']) }}
                                            @elseif(isset($paymentDetails['payment_type']))
                                                {{ ucfirst($paymentDetails['payment_type']) }}
                                            @else
                                                Tidak Ada
                                            @endif
                                        </p>
                                    @else
                                        <p class="mb-0">Tidak Ada</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Profile Form Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">PROFIL</h5>
                        <button type="submit" form="profile-form" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Profil
                        </button>
                    </div>
                    <div class="card-body">
                        <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div id="update-status" class="alert" style="display: none;"></div>

                            <!-- Personal Info -->
                            <div class="form-group">
                                <label>Nama</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-ellipsis-v"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $user->email }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="tel" class="form-control" name="phone_number"
                                    value="{{ $user->phone_number }}">
                            </div>

                            <!-- Location -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <select id="province" name="province" class="form-control">
                                            @if (!$user->province)
                                                <option selected disabled>Pilih Provinsi</option>
                                            @endif
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province['id'] }}"
                                                    {{ $user->province == $province['name'] ? 'selected' : '' }}>
                                                    {{ $province['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kota/Kabupaten</label>
                                        <select id="regency" name="regency" class="form-control"
                                            {{ !$user->province ? 'disabled' : '' }}>
                                            @if (!$user->regency)
                                                <option selected disabled>
                                                    {{ !$user->province ? 'Pilih Kota/Kabupaten' : 'Pilih Kota/Kabupaten' }}
                                                </option>
                                            @endif
                                            @if ($regencies)
                                                @foreach ($regencies as $regency)
                                                    <option value="{{ $regency['id'] }}"
                                                        {{ $user->regency == $regency['name'] ? 'selected' : '' }}>
                                                        {{ $regency['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceDropdown = document.getElementById('province');
            const regencyDropdown = document.getElementById('regency');
            const profileForm = document.getElementById('profile-form');
            const updateStatus = document.getElementById('update-status');
            const submitButton = document.querySelector(
                'button[type="submit"][form="profile-form"]'); // Perbaikan disini

            // Handle province selection change
            provinceDropdown.addEventListener('change', async function() {
                const provinceId = this.value;
                regencyDropdown.innerHTML = '<option selected disabled>Memuat data...</option>';
                regencyDropdown.disabled = true;

                try {
                    const response = await fetch(`/api/regencies/${provinceId}`);
                    const regencies = await response.json();

                    regencyDropdown.innerHTML =
                        '<option selected disabled>Pilih Kota/Kabupaten</option>';
                    regencies.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.id;
                        option.textContent = regency.name;
                        regencyDropdown.appendChild(option);
                    });
                    regencyDropdown.disabled = false;
                } catch (error) {
                    console.error('Error fetching regencies:', error);
                    regencyDropdown.innerHTML = '<option selected disabled>Gagal memuat data</option>';
                }
            });

            // Handle form submission
            profileForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                // Disable submit button if it exists
                if (submitButton) {
                    submitButton.disabled = true;
                }

                try {
                    console.log('Submitting form...');

                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || ''
                        }
                    });

                    console.log('Response status:', response.status);

                    const result = await response.json();
                    console.log('Response data:', result);

                    if (!response.ok) {
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }

                    updateStatus.className = 'alert alert-success';
                    updateStatus.textContent = result.message;
                    updateStatus.style.display = 'block';

                    setTimeout(() => {
                        updateStatus.style.display = 'none';
                    }, 3000);
                } catch (error) {
                    console.error('Error detail:', error);
                    updateStatus.className = 'alert alert-danger';
                    updateStatus.textContent = 'Terjadi kesalahan: ' + error.message;
                    updateStatus.style.display = 'block';
                } finally {
                    // Enable submit button if it exists
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                }
            });
        });
    </script>
@endpush
