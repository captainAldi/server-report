@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Detail Project: {{ $project->nama_project }}
    @if($project->trashed())
      <span class="badge badge-danger">SOFT DELETED</span>
    @endif
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.lokasi.gcp') }}">Lokasi GCP</a>
  </li>
  <li class="breadcrumb-item active">
    Detail
  </li>
@endsection

@section('content')

<!-- Info Boxes -->
<div class="row">
    <!-- Compute Engine -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-server"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Compute Engine</span>
                <span class="info-box-number">{{ $ce_list->count() }} Instances</span>
            </div>
        </div>
    </div>
    <!-- Cloud SQL -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cloud SQL</span>
                <span class="info-box-number">{{ $csql_list->count() }} Instances</span>
            </div>
        </div>
    </div>
    <!-- Cloud Storage -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cloud Storage</span>
                <span class="info-box-number">{{ $bucket_list->count() }} Buckets</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-ce" data-toggle="pill" href="#content-ce" role="tab" aria-controls="content-ce" aria-selected="true">Compute Engine</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-csql" data-toggle="pill" href="#content-csql" role="tab" aria-controls="content-csql" aria-selected="false">Cloud SQL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-bucket" data-toggle="pill" href="#content-bucket" role="tab" aria-controls="content-bucket" aria-selected="false">Cloud Storage</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    
                    <!-- Compute Engine Content -->
                    <div class="tab-pane fade show active" id="content-ce" role="tabpanel" aria-labelledby="tab-ce">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>IP Private</th>
                                    <th>IP Public</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ce_list as $ce)
                                <tr>
                                    <td>{{ $ce->nama }}</td>
                                    <td>{{ $ce->tipe }}</td>
                                    <td>{{ $ce->priv_ip }}</td>
                                    <td>{{ $ce->pub_ip }}</td>
                                    <td>
                                        @if($ce->status == 'RUNNING')
                                            <span class="badge badge-success">{{ $ce->status }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $ce->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data Compute Engine.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Cloud SQL Content -->
                    <div class="tab-pane fade" id="content-csql" role="tabpanel" aria-labelledby="tab-csql">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Versi DB</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($csql_list as $sql)
                                <tr>
                                    <td>{{ $sql->nama }}</td>
                                    <td>{{ $sql->tipe }}</td>
                                    <td>{{ $sql->db_ver }}</td>
                                    <td>
                                        <span class="badge badge-{{ $sql->status == 'RUNNABLE' ? 'success' : 'secondary' }}">
                                            {{ $sql->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data Cloud SQL.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Cloud Storage Content -->
                    <div class="tab-pane fade" id="content-bucket" role="tabpanel" aria-labelledby="tab-bucket">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Bucket</th>
                                    <th>Lokasi</th>
                                    <th>Tipe Storage</th>
                                    <th>Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bucket_list as $bucket)
                                <tr>
                                    <td>{{ $bucket->nama }}</td>
                                    <td>{{ $bucket->lokasi }}</td>
                                    <td>{{ $bucket->tipe_storage }}</td>
                                    <td>{{ $bucket->dibuat ? $bucket->dibuat->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data Bucket.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

@endsection
