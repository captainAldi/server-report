@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">Edit User</h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.user.index') }}">User</a>
  </li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Form Edit User</h3>
  </div>

  <form action="{{ route('patch.user.proses-ubah', $data_user->id) }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="card-body">
      <!-- Name Field -->
      <div class="form-group">
        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name', $data_user->name) }}"
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
               value="{{ old('email', $data_user->email) }}"
               placeholder="Masukkan email">
        @error('email')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password Field -->
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password"
               class="form-control @error('password') is-invalid @enderror"
               id="password"
               name="password"
               placeholder="Kosongkan jika tidak ingin mengubah password">
        <small class="form-text text-muted">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</small>
        @error('password')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password Confirmation Field -->
      <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password</label>
        <input type="password"
               class="form-control"
               id="password_confirmation"
               name="password_confirmation"
               placeholder="Ulangi password baru">
      </div>

      <!-- Role Field -->
      <div class="form-group">
        <label for="role">Role <span class="text-danger">*</span></label>
        <select class="form-control @error('role') is-invalid @enderror"
                id="role"
                name="role"
                {{ auth()->id() == $data_user->id ? 'disabled' : '' }}>
          <option value="">Pilih Role</option>
          <option value="admin" {{ old('role', $data_user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="staff" {{ old('role', $data_user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
        </select>

        @if(auth()->id() == $data_user->id)
          <input type="hidden" name="role" value="{{ $data_user->role }}">
          <small class="form-text text-muted">Anda tidak dapat mengubah role Anda sendiri</small>
        @endif

        @error('role')
          <span class="error invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Update
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
