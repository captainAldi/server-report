@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Detail Virtual Machine Proxmox
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.report.usage.proxmox') }}">Report Proxmox</a>
  </li>
  <li class="breadcrumb-item active">
    Detail VM
  </li>
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Informasi Virtual Machine</h3>
        <div class="card-tools">
          <a href="{{ route('get.report.usage.proxmox') }}" class="btn btn-sm btn-secondary">
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
                  <th style="width: 200px">VM ID</th>
                  <td>{{ $data_server->vm_id }}</td>
                </tr>
                <tr>
                  <th>Nama VM</th>
                  <td>{{ $data_server->nama }}</td>
                </tr>
                <tr>
                  <th>Node</th>
                  <td>{{ $data_server->lokasi_proxmox->nama_node }}</td>
                </tr>
                <tr>
                  <th>Datacenter</th>
                  <td>{{ $data_server->lokasi_proxmox->nama_lokasi }}</td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td>
                    @if ($data_server->status == 'running')
                      <span class="badge badge-success">RUNNING</span>
                    @elseif ($data_server->status == 'stopped')
                      <span class="badge badge-danger">STOPPED</span>
                    @else
                      <span class="badge badge-warning">{{ strtoupper($data_server->status) }}</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Private IP</th>
                  <td>{{ $data_server->priv_ip }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 200px">vCPU</th>
                  <td>{{ $data_server->v_cpu }} Core</td>
                </tr>
                <tr>
                  <th>Memory (RAM)</th>
                  <td>{{ $data_server->ram }} MB</td>
                </tr>
                <tr>
                  <th>Disk Storage</th>
                  <td>{{ $data_server->disk }} GB</td>
                </tr>
                <tr>
                  <th>CPU Usage</th>
                  <td>{{ $data_server->usage_cpu }}%</td>
                </tr>
                <tr>
                  <th>RAM Usage</th>
                  <td>{{ $data_server->usage_ram }}%</td>
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
          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="fas fa-microchip"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Virtual CPU</span>
                <span class="info-box-number">{{ $data_server->v_cpu }} Cores</span>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-memory"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Memory</span>
                <span class="info-box-number">{{ $data_server->ram }} MB</span>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fas fa-hdd"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Disk Storage</span>
                <span class="info-box-number">{{ $data_server->disk }} GB</span>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="fas fa-tachometer-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">CPU Usage</span>
                <span class="info-box-number">{{ $data_server->usage_cpu }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Usage & Recommendation -->
        <div class="row mt-3">
          <div class="col-md-6">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Current Usage</h3>
              </div>
              <div class="card-body">
                <table class="table table-sm">
                  <tbody>
                    <tr>
                      <th style="width: 150px">CPU Usage</th>
                      <td>
                        <div class="progress">
                          <div class="progress-bar bg-info" role="progressbar" style="width: {{ $data_server->usage_cpu }}%" aria-valuenow="{{ $data_server->usage_cpu }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $data_server->usage_cpu }}%
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <th>RAM Usage</th>
                      <td>
                        <div class="progress">
                          <div class="progress-bar bg-success" role="progressbar" style="width: {{ $data_server->usage_ram }}%" aria-valuenow="{{ $data_server->usage_ram }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $data_server->usage_ram }}%
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card card-outline card-success">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb"></i> Recommendation</h3>
              </div>
              <div class="card-body">
                <table class="table table-sm">
                  <tbody>
                    <tr>
                      <th style="width: 150px">Recommended CPU</th>
                      <td>{{ $data_server->rec_cpu }}%</td>
                    </tr>
                    <tr>
                      <th>Recommended RAM</th>
                      <td>{{ $data_server->rec_ram }}%</td>
                    </tr>
                  </tbody>
                </table>
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
                      <th>Node Host</th>
                      <td><code>{{ $data_server->lokasi_proxmox->ip_node }}:{{ $data_server->lokasi_proxmox->port_node }}</code></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        @if ($data_server->status == 'stopped')
        <div class="row mt-3">
          <div class="col-12 text-center">
            <a href="{{ route('get.proxmox.start-vm', ['id_node' => $data_server->lokasi_proxmox_id, 'id_vm' => $data_server->vm_id]) }}" 
               class="btn btn-success btn-lg"
               onclick="return confirm('Apakah Anda yakin ingin menjalankan VM ini?')">
              <i class="fas fa-play"></i> Start Virtual Machine
            </a>
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>

@endsection
