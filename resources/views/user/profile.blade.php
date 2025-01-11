@extends('layouts.app')

@section('title', 'User Profile')

@section('page-title', 'Profil User')

@push('breadcrumbs')
  <li class="breadcrumb-item active">
    <strong>Profil User</strong>
  </li>
@endpush

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Data Profil</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4">
              <div class="card mb-4">
                <div class="card-body text-center">
                  <div
                    class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary mb-3 text-white"
                    style="width: 80px; height: 80px;">
                    <span class="h3">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                  </div>
                  <h5 class="card-title">{{ $user->name }}</h5>
                  <p class="text-muted">{{ $user->email }}</p>

                  <!-- Menu Items -->
                  <div class="list-group text-left">
                    <a href="#" class="list-group-item list-group-item-action">
                      (CPNS) GRATIS <i class="fa fa-chevron-right float-right"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                      (BUMN) GRATIS <i class="fa fa-chevron-right float-right"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                      (POLRI) GRATIS <i class="fa fa-chevron-right float-right"></i>
                    </a>
                  </div>

                  <!-- Navigation Menu -->
                  <div class="list-group mt-4 text-left">
                    <a href="#" class="list-group-item list-group-item-action">
                      <i class="fa fa-user mr-2"></i> Pengaturan Akun
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                      <i class="fa fa-exchange-alt mr-2"></i> Transaksi Saya
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                      <i class="fa fa-ticket-alt mr-2"></i> Voucher Promo
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                      <i class="fa fa-handshake mr-2"></i> Program Afiliasi
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Main Profile Form -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-4">PROFIL</h4>

                  <form>
                    <!-- Personal Info -->
                    <div class="form-group">
                      <label>Nama</label>
                      <div class="input-group">
                        <input type="text" class="form-control" value="{{ $user->name }}">
                        <div class="input-group-append">
                          <span class="input-group-text"><i class="fa fa-ellipsis-v"></i></span>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Email</label>
                      <input type="email" class="form-control" value="{{ $user->email }}">
                    </div>

                    <div class="form-group">
                      <label>Nomor Telepon</label>
                      <input type="tel" class="form-control" value="{{ $user->phone_number ?? 'Belum diatur' }}">
                    </div>

                    <div class="form-group">
                      <label>Tanggal Lahir</label>
                      <div class="input-group">
                        <input type="text" class="form-control" value="22/03/1995">
                        <div class="input-group-append">
                          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                      </div>
                    </div>

                    <!-- Location -->
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Provinsi</label>
                          <select class="form-control">
                            <option selected>Nusa Tenggara Barat (NTB)</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Kota/Kabupaten</label>
                          <select class="form-control">
                            <option selected>Kota Mataram</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Jenis Kelamin</label>
                          <select class="form-control">
                            <option selected>Perempuan</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Profesi</label>
                          <input type="text" class="form-control" value="IT Support Jurnal Ilmiah Perguruan Tinggi">
                        </div>
                      </div>
                    </div>

                    <!-- Learning Preferences -->
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Cara Belajar</label>
                          <select class="form-control">
                            <option selected>Latihan soal</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Preferensi Belajar</label>
                          <select class="form-control">
                            <option selected>Belajar mandiri</option>
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
      </div>
    </div>
  </div>
@endsection
