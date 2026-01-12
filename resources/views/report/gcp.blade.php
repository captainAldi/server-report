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

@if ($cari_layanan != '')
<div class="row">
  <div class="col">
    
    <!-- small card -->
    <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $total_cpu_used }}</h3>

          <p>Total CPU Used (vCPU)</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-pie"></i>
        </div>

        <a href="#" class="small-box-footer">
          More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>

  </div>

  <div class="col">
    <!-- small card -->
    <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $total_ram_used }}</h3>

          <p>Total RAM Used (GB)</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-pie"></i>
        </div>

        <a href="#" class="small-box-footer">
          More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
  </div>

  <div class="col">
    <!-- small card -->
    <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $total_disk_used }}</h3>

          <p>Total Disk Used (GB)</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-pie"></i>
        </div>

        <a href="#" class="small-box-footer">
          More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
  </div>
</div>
@endif

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-header">
        <h3 class="class-title">Server</h3>
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
                <label for="cari_layanan">Services</label>
                <select name="cari_layanan" class="form-control">
                  <option value="">Pilih Opsi</option>
                  <option value="Compute Engine" {{ $cari_layanan == "Compute Engine" ? 'selected' : '' }}>Compute Engine</option>
                  <option value="Cloud SQL" {{ $cari_layanan == "Cloud SQL" ? 'selected' : '' }}>Cloud SQL</option>
                  <option value="Cloud Storage" {{ $cari_layanan == "Cloud Storage" ? 'selected' : '' }}>Cloud Storage</option>
                </select>
              </div>

              @if ($cari_layanan != '')
                <div class="col-lg-2 col-md-6 col-xs-12">
                  <label for="cari_nama">Nama</label>
                  <input type="text" name="cari_nama" class="form-control" placeholder="Instances ..." value="{{ $cari_nama }}">
                </div>

                <div class="col-lg-2 col-md-6 col-xs-12 form-group">
                  <label for="cari_lokasi">Lokasi</label>
                  <select name="cari_lokasi" class="form-control select2bs4">
                    <option value="">Pilih Opsi</option>
                    @foreach ($data_semua_lokasi as $lokasi)
                      <option value="{{ $lokasi->id }}" {{ $cari_lokasi == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->id_project }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-lg-2 col-md-6 col-xs-12">
                  <label for="cari_status">Status</label>
                  <select name="cari_status" class="form-control">
                    <option value="">Pilih Opsi</option>
                    @if ($cari_layanan == 'Compute Engine')
                      <option value="RUNNING" {{ $cari_status == "RUNNING" ? 'selected' : '' }}>Running</option>
                      <option value="TERMINATED" {{ $cari_status == "TERMINATED" ? 'selected' : '' }}>Terminated</option>
                      <option value="DELETED" {{ $cari_status == "DELETED" ? 'selected' : '' }}>Deleted</option>
                    @elseif ($cari_layanan == 'Cloud SQL')
                      <option value="RUNNABLE" {{ $cari_status == "RUNNABLE" ? 'selected' : '' }}>Runnable</option>
                      <option value="TERMINATED" {{ $cari_status == "TERMINATED" ? 'selected' : '' }}>Terminated</option>
                      <option value="DELETED" {{ $cari_status == "DELETED" ? 'selected' : '' }}>Deleted</option>
                    @elseif ($cari_layanan == 'Cloud Storage')
                      <option value="ACTIVE" {{ $cari_status == "ACTIVE" ? 'selected' : '' }}>Active</option>
                      <option value="DELETED" {{ $cari_status == "DELETED" ? 'selected' : '' }}>Deleted</option>
                    @endif
                  </select>
                </div>
              @endif

            </div>
              

            <div class="row m-2">
              <div class="col-lg-2 col-md-6 col-xs-12">
                <label for="set_pagination">Item per Page</label>

                <select name="set_pagination" class="form-control">
                  
                  <option value="10" {{ $set_pagination == "10" ? 'selected' : '' }}>10</option>
                  <option value="50" {{ $set_pagination == "50" ? 'selected' : '' }}>50</option>
                  <option value="100" {{ $set_pagination == "100" ? 'selected' : '' }}>100</option>
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
                  <option value="">Pilih Opsi</option>
                  <option value="created_at" {{ $var_sort == "created_at" ? 'selected' : '' }}>Created at</option>
                  <option value="updated_at" {{ $var_sort == "updated_at" ? 'selected' : '' }}>Updated at</option>
                </select>
              </div>

              <div class="col-lg-2 col-md-6 col-xs-12">
                <label for="tipe_sort"><i class="fa fa-arrow-up"></i> Type</label>
                <select name="tipe_sort" class="form-control">
                  <option value="">Pilih Opsi</option>
                  <option value="desc" {{ $tipe_sort == "desc" ? 'selected' : '' }}>Desc</option>
                  <option value="asc" {{ $tipe_sort == "asc" ? 'selected' : '' }}>Asc</option>
                </select>
              </div>
            </div>

          </div>
          <!-- /.sorting tab -->

          <div class="row ml-2">
            <div class="col">
              <button class="btn btn-primary" onclick="filterData()">Apply</button>

              <a href="{{ route('get.report.usage.gcp') }}" class="btn btn-primary ml-3">Refresh</a>

              @if ($cari_layanan != '')
                <button class="btn btn-success" onclick="exportToExcel()">
                  <i class="fa-solid fa-file-excel"></i>
                </button>
              @endif
            </div>

          </div>

        </div>
        <!-- /.tab content -->

      </form>

      




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
          @elseif ($cari_layanan == 'Cloud Storage')
            <a class="btn btn-primary mr-3 mb-2 mt-2 " href="{{ route('get.report.usage.gcp.bucket.sync') }}" >
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
              @foreach($data_semua_ce as $server)
                <tr class="{{ $server->trashed() ? 'table-danger' : '' }}">

                  <td>{{ $loop->iteration + $data_semua_ce->firstItem() - 1 }}</td>
                  <td>
                    {{ $server->nama }}
                    @if($server->trashed())
                      <span class="badge badge-danger">SOFT DELETED</span>
                    @endif
                  </td>
                  <td>{{ $server->lokasi_gcp ? $server->lokasi_gcp->id_project : '-' }}</td>
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

        <div class="card-footer clearfix">
          <h5>Jumlah Data : <span>{{ $data_semua_ce->total() }}</span></h5>
          {{ $data_semua_ce->links('vendor.pagination.adminlte-3') }}
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
                @foreach($data_semua_csql as $csql)
                  <tr class="{{ $csql->trashed() ? 'table-danger' : '' }}">

                    <td>{{ $loop->iteration + $data_semua_csql->firstItem() - 1 }}</td>
                    <td>
                      {{ $csql->nama }}
                      @if($csql->trashed())
                        <span class="badge badge-danger">SOFT DELETED</span>
                      @endif
                    </td>
                    <td>{{ $csql->lokasi_gcp ? $csql->lokasi_gcp->id_project : '-' }}</td>
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

          <div class="card-footer clearfix">
            <h5>Jumlah Data : <span>{{ $data_semua_csql->total() }}</span></h5>
            {{ $data_semua_csql->links('vendor.pagination.adminlte-3') }}
          </div>

        </div>
      @elseif ($cari_layanan == 'Cloud Storage')
        <div class="card-body table-responsive">
          <table class="table table-bordered table-head-fixed">
            <thead>
              <tr>
                <th style="width: 10px">No</th>
                <th>Nama Bucket</th>
                <th>Lokasi</th>
                <th>Tipe Storage</th>
                <th>Dibuat</th>
                <th>Status</th>
                <th class="text-nowrap">
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($data_semua_bucket as $bucket)
                <tr class="{{ $bucket->trashed() ? 'table-danger' : '' }}">

                  <td>{{ $loop->iteration + $data_semua_bucket->firstItem() - 1 }}</td>
                  <td>
                    {{ $bucket->nama }}
                    @if($bucket->trashed())
                      <span class="badge badge-danger">SOFT DELETED</span>
                    @endif
                  </td>
                  <td>{{ $bucket->lokasi_gcp ? $bucket->lokasi_gcp->id_project : '-' }}</td>
                  <td>{{ $bucket->tipe_storage }}</td>
                  <td>{{ $bucket->dibuat }}</td>
                  <td>
                    @if($bucket->trashed())
                      DELETED
                    @else
                      ACTIVE
                    @endif
                  </td>

                  <td class="text-nowrap">
                    <!-- Detail -->
                    <a href="{{ route('get.report.usage.gcp.detail.bucket', $bucket->id) }}" class="btn btn-warning" aria-disabled="">
                      <i class="fa fa-eye"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer clearfix">
          <h5>Jumlah Data : <span>{{ $data_semua_bucket->total() }}</span></h5>
          {{ $data_semua_bucket->links('vendor.pagination.adminlte-3') }}
        </div>

      @else
        <div class="card-body">
          Silahkan Pilih Service !
        </div>
      @endif
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>

  // Select 2
  $(document).ready(function() {
    $('.select2').select2();
  });
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  });

  // Manipulate Form-Filter
  let formFilter = document.getElementById("form-filter");
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);

  function filterData() {
    formFilter.action = "{{ route('get.report.usage.gcp') }}"
    formFilter.submit();
  };

  function exportToExcel() {
    let layanan = urlParams.get('cari_layanan')

    if (layanan == 'Compute Engine') {
      formFilter.action = "{{ route('get.report.usage.gcp.ce.excel') }}"
    } else if (layanan == 'Cloud SQL') {
      formFilter.action = "{{ route('get.report.usage.gcp.csql.excel') }}"
    } else if (layanan == 'Cloud Storage') {
      formFilter.action = "{{ route('get.report.usage.gcp.bucket.excel') }}"
    }

    formFilter.submit();
  };
</script>
@endpush