@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Data Lokasi GCP
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.lokasi.gcp') }}">Lokasi GCP</a>
  </li>
@endsection


@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="class-title">Projects</h3>
  </div>

  <form id="form-filter" action="" method="get">
    <!-- Header Tab -->
    <ul class="nav nav-tabs" id="tab-users" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="filter-tab" data-toggle="tab" href="#filter" role="tab" aria-controls="filter" aria-selected="true">Filter</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="sort-tab" data-toggle="tab" href="#sort" role="tab" aria-controls="sort" aria-selected="false">Sort</a>
      </li>
    </ul>
    <!-- /.header tab -->

    <!-- Tab Content -->
    <div class="tab-content" id="tab-usersContent">
      <!-- Filter Tab -->
      <div class="tab-pane fade show active" id="filter" role="tabpanel" aria-labelledby="filter-tab">
        <div class="row ml-2 mb-2">
          <div class="col-lg-2 col-md-6 col-xs-12">
            <label for="cari_nama">Nama</label>
            <input type="text" name="cari_nama" class="form-control" placeholder="Nama Project ..." value="{{ $cari_nama ?? '' }}">
          </div>
        </div>

        <div class="row m-2">
          <div class="col-lg-2 col-md-6 col-xs-12">
            <label for="set_pagination">Item per Page</label>
            <select name="set_pagination" class="form-control">
              <option value="10" {{ ($set_pagination ?? 10) == "10" ? 'selected' : '' }}>10</option>
              <option value="50" {{ ($set_pagination ?? 10) == "50" ? 'selected' : '' }}>50</option>
              <option value="100" {{ ($set_pagination ?? 10) == "100" ? 'selected' : '' }}>100</option>
            </select>
          </div>
        </div>
      </div>
      <!-- /.filter tab -->

      <!-- Sorting Tab -->
      <div class="tab-pane fade" id="sort" role="tabpanel" aria-labelledby="sort-tab">
        <div class="row m-2">
          <div class="col-lg-2 col-md-6 col-xs-12">
            <label for="var_sort"><i class="fa fa-arrow-down"></i> Field</label>
            <select name="var_sort" class="form-control">
              <option value="nama_project" {{ ($var_sort ?? 'nama_project') == "nama_project" ? 'selected' : '' }}>Nama Project</option>
              <option value="id_project" {{ ($var_sort ?? 'nama_project') == "id_project" ? 'selected' : '' }}>ID Project</option>
              <option value="dibuat" {{ ($var_sort ?? 'nama_project') == "dibuat" ? 'selected' : '' }}>Dibuat</option>
              <option value="created_at" {{ ($var_sort ?? 'nama_project') == "created_at" ? 'selected' : '' }}>Created at</option>
              <option value="updated_at" {{ ($var_sort ?? 'nama_project') == "updated_at" ? 'selected' : '' }}>Updated at</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 col-xs-12">
            <label for="tipe_sort"><i class="fa fa-arrow-up"></i> Type</label>
            <select name="tipe_sort" class="form-control">
              <option value="asc" {{ ($tipe_sort ?? 'asc') == "asc" ? 'selected' : '' }}>Asc</option>
              <option value="desc" {{ ($tipe_sort ?? 'asc') == "desc" ? 'selected' : '' }}>Desc</option>
            </select>
          </div>
        </div>
      </div>
      <!-- /.sorting tab -->

      <div class="row ml-2">
        <div class="col">
          <button class="btn btn-primary" onclick="filterData()">Apply</button>
          <a href="{{ route('get.lokasi.gcp') }}" class="btn btn-primary ml-3">Refresh</a>
        </div>
      </div>
    </div>
    <!-- /.tab content -->
  </form>

  <div class="row">
    <div class="col">
      <h4 class="ml-3 mt-2">Jumlah: {{ $data_lokasi->total() }}</h4>
    </div>
  </div>

  <!-- Button Sync -->
  <div class="row">
    <div class="col-md-2">
      <a class="btn btn-primary ml-3 mb-2 mt-2 " href="{{ route('get.lokasi.gcp.sync') }}" >
        <i class="fa-solid fa-rotate">
        </i>
      </a>
    </div>
  </div>
  <!-- /.button sync -->

  <div class="card-body table-responsive">
    <table class="table table-bordered table-head-fixed">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Nama</th>
          <th>ID Project</th>
          <th>Di Buat</th>
          <th class="text-nowrap">
            Action
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach($data_lokasi as $lokasi)
          <tr class="{{ $lokasi->trashed() ? 'table-danger' : '' }}">
            <td>{{ $loop->iteration + $data_lokasi->firstItem() - 1 }}</td>
            <td>
              {{ $lokasi->nama_project }}
              @if($lokasi->trashed())
                <span class="badge badge-danger">SOFT DELETED</span>
              @endif
            </td>
            <td>{{ $lokasi->id_project }}</td>
            <td>{{ $lokasi->dibuat }}</td>

            <td class="text-nowrap">
              <!-- Detail -->
              <a href="{{ route('get.lokasi.gcp.detail', $lokasi->id) }}" class="btn btn-warning" aria-disabled="">
                <i class="fa fa-eye"></i>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="card-footer clearfix">
    <h5>Jumlah Data : <span>{{ $data_lokasi->total() }}</span></h5>
    {{ $data_lokasi->links('vendor.pagination.adminlte-3') }}
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Manipulate Form-Filter
  let formFilter = document.getElementById("form-filter");

  function filterData() {
    formFilter.action = "{{ route('get.lokasi.gcp') }}"
    formFilter.submit();
  };
</script>
@endpush