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

  <div class="row">
    <div class="col">
      <h4 class="ml-3 mt-2">Jumlah: {{ $data_lokasi->count() }}</h4>
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
          <tr>

            <td>{{ $loop->iteration }}</td>
            <td>{{ $lokasi->nama_project }}</td>
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
</div>
@endsection

@push('scripts')

@endpush