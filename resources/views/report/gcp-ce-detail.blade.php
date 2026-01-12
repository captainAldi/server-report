@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Detail Compute Engine
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.report.usage.gcp') }}">Report GCP</a>
  </li>
  <li class="breadcrumb-item active">
    Detail CE
  </li>
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Informasi Compute Engine</h3>
        <div class="card-tools">
          <a href="{{ route('get.report.usage.gcp') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 200px">Instance ID</th>
                  <td>{{ $data_server->ce_id }}</td>
                </tr>
                <tr>
                  <th>Nama Instance</th>
                  <td>{{ $data_server->nama }}</td>
                </tr>
                <tr>
                  <th>Project</th>
                  <td>{{ $data_server->lokasi_gcp ? $data_server->lokasi_gcp->id_project : '-' }}</td>
                </tr>
                <tr>
                  <th>Tipe Machine</th>
                  <td>{{ $data_server->tipe }}</td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td>
                    @if ($data_server->status == 'RUNNING')
                      <span class="badge badge-success">{{ $data_server->status }}</span>
                    @elseif ($data_server->status == 'TERMINATED')
                      <span class="badge badge-danger">{{ $data_server->status }}</span>
                    @else
                      <span class="badge badge-warning">{{ $data_server->status }}</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Tanggal Dibuat</th>
                  <td>{{ $data_server->dibuat->format('d M Y, H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 200px">Private IP</th>
                  <td>{{ $data_server->priv_ip }}</td>
                </tr>
                <tr>
                  <th>Public IP</th>
                  <td>{{ $data_server->pub_ip }}</td>
                </tr>
                <tr>
                  <th>vCPU</th>
                  <td>{{ $data_server->v_cpu }} Core</td>
                </tr>
                <tr>
                  <th>Memory (RAM)</th>
                  <td>{{ $data_server->ram }} GB</td>
                </tr>
                <tr>
                  <th>Disk Storage</th>
                  <td>{{ $data_server->disk }} GB</td>
                </tr>
                <tr>
                  <th>Last Updated</th>
                  <td>{{ $data_server->updated_at->format('d M Y, H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Resource Summary -->
        <div class="row mt-4">
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="fas fa-microchip"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Virtual CPU</span>
                <span class="info-box-number">{{ $data_server->v_cpu }} Cores</span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-memory"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Memory</span>
                <span class="info-box-number">{{ $data_server->ram }} GB</span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fas fa-hdd"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Disk Storage</span>
                <span class="info-box-number">{{ $data_server->disk }} GB</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Network Information -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-network-wired"></i> Network Information</h3>
              </div>
              <div class="card-body">
                <table class="table table-sm">
                  <tbody>
                    <tr>
                      <th style="width: 200px">Private IP Address</th>
                      <td><code>{{ $data_server->priv_ip }}</code></td>
                    </tr>
                    <tr>
                      <th>Public IP Address</th>
                      <td><code>{{ $data_server->pub_ip }}</code></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection
