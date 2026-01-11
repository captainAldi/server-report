@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Detail Bucket GCS
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.report.usage.gcp') }}">Report GCP</a>
  </li>
  <li class="breadcrumb-item active">
    Detail Bucket
  </li>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Detail Bucket</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-database"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Nama Bucket</span>
                  <span class="info-box-number">{{ $data_bucket->nama }}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-project-diagram"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Project</span>
                  <span class="info-box-number">{{ $data_bucket->lokasi_gcp->id_project ?? '-' }}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-map-marker-alt"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Lokasi</span>
                  <span class="info-box-number">{{ $data_bucket->lokasi ?? '-' }}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hdd"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Tipe Storage</span>
                  <span class="info-box-number">{{ $data_bucket->tipe_storage ?? '-' }}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="far fa-calendar-alt"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Dibuat</span>
                  <span class="info-box-number">{{ $data_bucket->dibuat ? $data_bucket->dibuat->format('d M Y H:i') : '-' }}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1">
                  @if($data_bucket->trashed())
                    <i class="fas fa-trash-alt"></i>
                  @else
                    <i class="fas fa-check-circle"></i>
                  @endif
                </span>

                <div class="info-box-content">
                  <span class="info-box-text">Status</span>
                  <span class="info-box-number">
                    @if($data_bucket->trashed())
                      DELETED
                    @else
                      ACTIVE
                    @endif
                  </span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </div>
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <a href="{{ route('get.report.usage.gcp') }}?cari_layanan=Cloud Storage" class="btn btn-default">Kembali</a>
        </div>
        <!-- /.card-footer-->
      </div>
      <!-- /.card -->
    </div>
  </div>
</div>
@endsection