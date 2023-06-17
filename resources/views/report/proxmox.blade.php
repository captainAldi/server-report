@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Data Report Proxmox
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.report.usage.proxmox') }}">Report Proxmox</a>
  </li>
@endsection


@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="class-title">Server</h3>
  </div>

  <div class="row ml-2">
    <div class="col">
      <h4>Filter</h4>
    </div>
  </div>


  <form action="{{ route('get.report.usage.proxmox') }}" method="get">

    <div class="row ml-2 mb-2">

      <div class="col-lg-2 col-md-6 col-xs-12">
        <label for="cari_node">Node</label>
        <select name="cari_node" class="form-control">
          <option value="">Pilih Opsi</option>
          @foreach ($data_nama_node as $node)
            <option value="{{ $node->id }}" {{ $cari_node == $node->id ? 'selected' : '' }}>{{ $node->nama_node }}</option>
          @endforeach
        </select>
      </div>

    </div>

    <div class="row ml-2">
      <div class="col">
        <button type="submit" class="btn btn-primary">Apply</button>

        <a href="{{ route('get.report.usage.proxmox') }}" class="btn btn-primary ml-3">Refresh</a>
      </div>

    </div>

  </form>

  <div class="row">
    <div class="col text-right">
      <h4 class="mr-3 mt-2">
        @if (!empty($cari_node))
          Jumlah: {{ $data_server->count() }}
        @else
          Jumlah: 0
        @endif
      </h4>
    </div>
  </div>

  <!-- Button Sync -->
  <div class="row">
    <div class="col text-right">
      <a class="btn btn-primary mr-3 mb-2 mt-2 " href="{{ route('get.report.usage.proxmox.vm.sync') }}" >
        <i class="fa-solid fa-rotate">
        </i>
      </a>
    </div>
  </div>
  <!-- /.button sync -->

  @if(!empty($cari_node))
    <div class="card-body table-responsive">
      <table class="table table-bordered table-head-fixed">
        <thead>
          <tr>
            <th style="width: 10px">No</th>
            <th>Nama</th>
            <th>Node</th>
            <th>VM ID</th>
            <th>Private IP</th>
            <th>vCPU</th>
            <th>RAM</th>
            <th>Disk</th>
            <th>Status</th>
            <th>Dibuat</th>
            <th class="text-nowrap">
              Action
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach($data_server as $server)
            <tr>

              <td>{{ $loop->iteration }}</td>
              <td>
                {{ $server->nama }}
                
                @if ($server->rec_ram != 'None')
                  <button type="button" class="btn btn-xs btn-warning ml-3 mb-2 mt-2" data-toggle="modal" data-target="#modal-insight-{{$server->id}}">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                @endif

              </td>
              <td>{{ $server->lokasi_proxmox->nama_node }}</td>
              <td>{{ $server->vm_id }}</td>
              <td>{{ $server->priv_ip }}</td>
              <td>{{ $server->v_cpu }}</td>
              <td>{{ $server->ram }}</td>
              <td>{{ $server->disk }}</td>
              <td>{{ $server->status }}</td>
              <td>{{ $server->dibuat }}</td>

              <td class="text-nowrap">
                <!-- Detail -->
                <a href="{{ route('get.report.usage.proxmox.detail', $server->id) }}" class="btn btn-info" aria-disabled=""> 
                  <i class="fa fa-eye"></i>
                </a>
              </td>
            </tr>

            {{-- Modal Insight --}}
            <div class="modal fade" id="modal-insight-{{$server->id}}">
              <div class="modal-dialog">

                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Insight Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <div class="modal-body">
                    <b>Usage</b>
                    <ul>
                      <li>CPU: {{ $server->usage_cpu }}</li>
                      <li>RAM: {{ $server->usage_ram }}</li>
                    </ul>

                    <b>Recomendation</b>
                    <ul>
                      <li>CPU: {{ $server->rec_cpu }}</li>
                      <li>RAM: {{ $server->rec_ram }}</li>
                    </ul>
                  </div>

                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="card-body">
      Silahkan Pilih Node !
    </div>
  @endif
</div>

@endsection

@push('scripts')

@endpush