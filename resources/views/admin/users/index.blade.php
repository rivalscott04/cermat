@extends('layouts.app')

@section('title', 'User') <!-- Judul untuk <title> -->

@section('page-title', 'Tabel User') <!-- Judul untuk Breadcrumb -->

@push('breadcrumbs')
  <li class="breadcrumb-item active">
    <strong>Tabel User</strong>
  </li>
@endpush

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox">
            <div class="ibox-title">
              <h5>Data user</h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
              </div>
            </div>
            <div class="ibox-content">
              <div class="table-responsive">
                <table class="table-striped table-bordered table-hover dataTables-example table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone Number</th>
                      <th>Is Active</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($users as $user)
                      <tr class="{{ $user->is_active ? 'gradeA' : 'gradeC' }}">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                        <td class="center">
                          <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <select name="is_active" onchange="this.form.submit()" class="form-control">
                              <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                              <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center">No users found</td>
                      </tr>
                    @endforelse
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
