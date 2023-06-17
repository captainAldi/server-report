@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Data Report GCP
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.report.usage.gcp') }}">Report GCP</a>
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


  <form action="{{ route('get.report.usage.gcp') }}" method="get">

    <div class="row ml-2 mb-2">

      <div class="col-lg-2 col-md-6 col-xs-12">
        <label for="cari_layanan">Services</label>
        <select name="cari_layanan" class="form-control">
          <option value="">Pilih Opsi</option>
          <option value="Compute Engine" {{ $cari_layanan == "Compute Engine" ? 'selected' : '' }}>Compute Engine</option>
          <option value="Cloud SQL" {{ $cari_layanan == "Cloud SQL" ? 'selected' : '' }}>Cloud SQL</option>
        </select>
      </div>

    </div>

    <div class="row ml-2">
      <div class="col">
        <button type="submit" class="btn btn-primary">Apply</button>

        <a href="{{ route('get.report.usage.gcp') }}" class="btn btn-primary ml-3">Refresh</a>
      </div>

    </div>

  </form>

  <div class="row">
    <div class="col text-right">
      <h4 class="mr-3 mt-2">
        @if ($cari_layanan == 'Compute Engine')
          Jumlah: {{ $data_server->count() }}
        @elseif ($cari_layanan == 'Cloud SQL')
          Jumlah: {{ $data_csql->count() }}
        @else
          Jumlah: 0
        @endif
      </h4>
    </div>
  </div>

  <!-- Button Sync -->
  <div class="row">
    <div class="col text-right">
      @if ($cari_layanan == 'Compute Engine')
        <a class="btn btn-primary mr-3 mb-2 mt-2 " href="{{ route('get.report.usage.gcp.ce.sync') }}" >
          <i class="fa-solid fa-rotate">
          </i>
        </a>
      @elseif ($cari_layanan == 'Cloud SQL')
        <a class="btn btn-primary mr-3 mb-2 mt-2 " href="{{ route('get.report.usage.gcp.csql.sync') }}" >
          <i class="fa-solid fa-rotate">
          </i>
        </a>
      @else
        <a class="btn btn-secondary mr-3 mb-2 mt-2 " href="#" >
          <i class="fa-solid fa-rotate">
          </i>
        </a>
      @endif
      
    </div>
  </div>
  <!-- /.button sync -->

  @if($cari_layanan == 'Compute Engine')
    <div class="card-body table-responsive">
      <table class="table table-bordered table-head-fixed">
        <thead>
          <tr>
            <th style="width: 10px">No</th>
            <th>Nama</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Private IP</th>
            <th>Public IP</th>
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
              <td>{{ $server->nama }}</td>
              <td>{{ $server->lokasi_gcp->id_project }}</td>
              <td>{{ $server->tipe }}</td>
              <td>{{ $server->priv_ip }}</td>
              <td>{{ $server->pub_ip }}</td>
              <td>{{ $server->v_cpu }}</td>
              <td>{{ $server->ram }}</td>
              <td>{{ $server->disk }}</td>
              <td>{{ $server->status }}</td>
              <td>{{ $server->dibuat }}</td>

              <td class="text-nowrap">
                <!-- Detail -->
                <a href="{{ route('get.report.usage.gcp.detail.ce', $server->id) }}" class="btn btn-warning" aria-disabled=""> 
                  <i class="fa fa-eye"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
  @elseif ($cari_layanan == 'Cloud SQL')
    <div class="card-body table-responsive">
      <div class="card-body table-responsive">
        <table class="table table-bordered table-head-fixed">
          <thead>
            <tr>
              <th style="width: 10px">No</th>
              <th>Nama</th>
              <th>Lokasi</th>
              <th>Tipe</th>
              <th>Public IP</th>
              <th>vCPU</th>
              <th>RAM</th>
              <th>Disk</th>
              <th>DB Version</th>
              <th>Status</th>
              <th>Dibuat</th>
              <th class="text-nowrap">
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($data_csql as $csql)
              <tr>

                <td>{{ $loop->iteration }}</td>
                <td>{{ $csql->nama }}</td>
                <td>{{ $csql->lokasi_gcp->id_project }}</td>
                <td>{{ $csql->tipe }}</td>
                <td>{{ $csql->pub_ip }}</td>
                <td>{{ $csql->v_cpu }}</td>
                <td>{{ $csql->ram }}</td>
                <td>{{ $csql->disk }}</td>
                <td>{{ $csql->db_ver }}</td>
                <td>{{ $csql->status }}</td>
                <td>{{ $csql->dibuat }}</td>

                <td class="text-nowrap">
                  <!-- Detail -->
                  <a href="{{ route('get.report.usage.gcp.detail.csql', $csql->id) }}" class="btn btn-warning" aria-disabled=""> 
                    <i class="fa fa-eye"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="card-body">
      Silahkan Pilih Service !
    </div>
  @endif
</div>

@endsection

@push('scripts')

@endpush