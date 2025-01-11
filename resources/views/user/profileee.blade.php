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
      <div class="ibox">
        <div class="ibox-title">
          <h5>Data Profil</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
          </div>
        </div>
        <div class="ibox-content">
          <form>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" id="name" class="form-control" value="{{ $user->name }}" readonly>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" id="phone_number" class="form-control" value="{{ $user->phone_number }}" readonly>
                </div>
                <div class="form-group">
                  <label for="is_active">Status</label>
                  <input type="text" id="is_active" class="form-control"
                    value="{{ $user->is_active ? 'Active' : 'Inactive' }}" readonly>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
