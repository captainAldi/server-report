@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">Tambah User</h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.user.index') }}">User</a>
  </li>
  <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Form Tambah User</h3>
  </div>

  <form action="{{ route('post.user.proses-simpan') }}" method="POST">
    @csrf

    <div class="card-body">
      <!-- Name Field -->
      <div class="form-group">
        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name') }}"
               placeholder="Masukkan nama lengkap"
               autofocus>
        @error('name')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <!-- Email Field -->
      <div class="form-group">
        <label for="email">Email <span class="text-danger">*</span></label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email') }}"
               placeholder="Masukkan email">
        @error('email')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password Field -->
      <div class="form-group">
        <label for="password">Password <span class="text-danger">*</span></label>
        <input type="password"
               class="form-control @error('password') is-invalid @enderror"
               id="password"
               name="password"
               placeholder="Minimal 8 karakter">
        @error('password')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password Confirmation Field -->
      <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
        <input type="password"
               class="form-control"
               id="password_confirmation"
               name="password_confirmation"
               placeholder="Ulangi password">
      </div>

      <!-- Role Field -->
      <div class="form-group">
        <label for="role">Role <span class="text-danger">*</span></label>
        <select class="form-control @error('role') is-invalid @enderror"
                id="role"
                name="role">
          <option value="">Pilih Role</option>
          <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
        </select>
        @error('role')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
      </button>
      <a href="{{ route('get.user.index') }}" class="btn btn-default">
        <i class="fas fa-times"></i> Batal
      </a>
    </div>
  </form>
</div>

@endsection

@push('scripts')
<script>
// Show flash messages
@if(session('pesan'))
    toastr.success('{{ session('pesan') }}');
@endif

@if(session('kesalahan'))
    toastr.error('{{ session('kesalahan') }}');
@endif
</script>
@endpush
