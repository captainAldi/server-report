@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">Manajemen User</h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item active">User</li>
@endsection

@section('content')

<!-- Statistics Cards -->
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $total_admin }}</h3>
        <p>Admin</p>
      </div>
      <div class="icon">
        <i class="fas fa-user-shield"></i>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $total_staff }}</h3>
        <p>Staff</p>
      </div>
      <div class="icon">
        <i class="fas fa-user"></i>
      </div>
    </div>
  </div>
</div>

<!-- Search and Filter -->
<div class="card">
  <div class="card-body">
    <form method="GET" action="{{ route('get.user.index') }}">
      <div class="row">
        <div class="col-md-4">
          <input type="text"
                 name="cari_nama"
                 class="form-control"
                 placeholder="Cari Nama / Email"
                 value="{{ $cari_nama }}">
        </div>
        <div class="col-md-3">
          <select name="cari_role" class="form-control">
            <option value="">Semua Role</option>
            <option value="admin" {{ $cari_role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="staff" {{ $cari_role == 'staff' ? 'selected' : '' }}>Staff</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Cari
          </button>
        </div>
        <div class="col-md-3 text-right">
          <a href="{{ route('get.user.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah User
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- User List Table -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Daftar User</h3>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-bordered table-head-fixed">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Email Verified</th>
          <th>Dibuat</th>
          <th class="text-nowrap">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data_users as $user)
          <tr>
            <td>{{ $loop->iteration + ($data_users->currentPage() - 1) * $data_users->perPage() }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
              @if($user->role == 'admin')
                <span class="badge badge-danger">Admin</span>
              @else
                <span class="badge badge-info">Staff</span>
              @endif
            </td>
            <td>
              @if($user->email_verified_at)
                <span class="badge badge-success">Verified</span>
              @else
                <span class="badge badge-warning">Not Verified</span>
              @endif
            </td>
            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
            <td class="text-nowrap">
              <a href="{{ route('get.user.edit', $user->id) }}"
                 class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
              </a>

              @if(auth()->id() !== $user->id)
                <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                  <i class="fas fa-trash"></i>
                </button>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">Tidak ada data user</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $data_users->appends(request()->query())->links() }}
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(userId, userName) {
    Swal.fire({
        title: 'Hapus User?',
        text: "Anda yakin ingin menghapus " + userName + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/user/delete/' + userId;

            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Show flash messages
@if(session('pesan'))
    toastr.success('{{ session('pesan') }}');
@endif

@if(session('kesalahan'))
    toastr.error('{{ session('kesalahan') }}');
@endif
</script>
@endpush
