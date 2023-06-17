@extends('layouts.dashboard')

@section('header')
  <h1 class="m-0 text-dark">
    Data Lokasi Proxmox
  </h1>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('get.lokasi.proxmox') }}">Lokasi Proxmox</a>
  </li>
@endsection


@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="class-title">Node</h3>
  </div>

  <div class="row">
    <div class="col">
      <h4 class="ml-3 mt-2">Jumlah: {{ $data_lokasi->count() }}</h4>
    </div>
  </div>

  <!-- Button Sync -->
  <div class="row">
    <div class="col-md-2">
      <button type="button" class="btn btn-primary ml-3 mb-2 mt-2" data-toggle="modal" data-target="#modal-add">
        <i class="fa-solid fa-plus">

        </i>
      </button>
    </div>
  </div>
  <!-- /.button sync -->

  <div class="card-body table-responsive">
    <table class="table table-bordered table-head-fixed">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Nama</th>
          <th>IP</th>
          <th>Port</th>
          <th class="text-nowrap">
            Action
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach($data_lokasi as $lokasi)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $lokasi->nama_node }}</td>
            <td>{{ $lokasi->ip_node }}</td>
            <td>{{ $lokasi->port_node }}</td>

            <td class="text-nowrap">
              <!-- Detail -->
              {{-- <a href="{{ route('get.lokasi.gcp.detail', $lokasi->id) }}" class="btn btn-warning" aria-disabled=""> 
                <i class="fa fa-eye"></i>
              </a> --}}

              <button type="button" class="btn btn-primary ml-3 mb-2 mt-2" data-toggle="modal" data-target="#modal-edit-{{$lokasi->id}}">
                <i class="fa-solid fa-edit"></i>
              </button>
            </td>
          </tr>

          {{-- Modal Edit --}}
          <div class="modal fade modal-edit" id="modal-edit-{{$lokasi->id}}">
            <div class="modal-dialog">

              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Edit Data</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <form action="{{ route('patch.lokasi.proxmox.proses-ubah', $lokasi->id) }}" method="post">
                  @csrf
                  @method('patch')

                  <div class="modal-body">
                    
                    <div class="form-group row">
                      <label for="nama_node_update" class="col-sm-2 col-form-label">Node</label>
                      <div class="col">
                        <input id="nama_node_update" type="text" class="form-control @error('nama_node_update') is-invalid @enderror" name="nama_node_update" value="{{ old('nama_node_update', $lokasi->nama_node) }}"  autocomplete="nama_node_update" autofocus placeholder="Nama Node">

                        @error('nama_node_update')
                          <span class="error invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                      
                    </div>

                    <div class="form-group row">
                      <label for="ip_node_update" class="col-sm-2 col-form-label">IP</label>
                      <div class="col">
                        <input id="ip_node_update" type="text" class="form-control @error('ip_node_update') is-invalid @enderror" name="ip_node_update" value="{{ old('ip_node_update', $lokasi->ip_node) }}"  autocomplete="ip_node_update" autofocus placeholder="IP Node">

                        @error('ip_node_update')
                          <span class="error invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="port_node_update" class="col-sm-2 col-form-label">Port</label>
                      <div class="col">
                        <input id="port_node_update" type="text" class="form-control @error('port_node_update') is-invalid @enderror" name="port_node_update" value="{{ old('port_node_update', $lokasi->port_node) }}"  autocomplete="port_node_update" autofocus placeholder="Port Node">

                        @error('port_node_update')
                          <span class="error invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="token_update" class="col-sm-2 col-form-label">Token</label>
                      <div class="col">
                        <input id="token_update" type="password" class="form-control @error('token_update') is-invalid @enderror" name="token_update" value="{{ old('token_update') }}"  autocomplete="token_update" autofocus placeholder="Kosongkan Jika Tidak ingin di Update !">

                        @error('token_update')
                          <span class="error invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                  </div>
                  
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>

                </form>
              </div>
            </div>
          </div>

  </div>
</div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Add --}}
<div class="modal fade" id="modal-add">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Data</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ route('post.lokasi.proxmox.proses-simpan') }}" method="post">
        @csrf

        <div class="modal-body">
          
          <div class="form-group row">
            <label for="nama_node" class="col-sm-2 col-form-label">Node</label>
            <div class="col">
              <input id="nama_node" type="text" class="form-control @error('nama_node') is-invalid @enderror" name="nama_node" value="{{ old('nama_node') }}"  autocomplete="nama_node" autofocus placeholder="Nama Node">

               @error('nama_node')
                <span class="error invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            
          </div>

          <div class="form-group row">
            <label for="ip_node" class="col-sm-2 col-form-label">IP</label>
            <div class="col">
              <input id="ip_node" type="text" class="form-control @error('ip_node') is-invalid @enderror" name="ip_node" value="{{ old('ip_node') }}"  autocomplete="ip_node" autofocus placeholder="IP Node">

              @error('ip_node')
                <span class="error invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>

          <div class="form-group row">
            <label for="port_node" class="col-sm-2 col-form-label">Port</label>
            <div class="col">
              <input id="port_node" type="text" class="form-control @error('port_node') is-invalid @enderror" name="port_node" value="{{ old('port_node') }}"  autocomplete="port_node" autofocus placeholder="Port Node">

              @error('port_node')
                <span class="error invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>

          <div class="form-group row">
            <label for="token" class="col-sm-2 col-form-label">Token</label>
            <div class="col">
              <input id="token" type="password" class="form-control @error('token') is-invalid @enderror" name="token" value="{{ old('token') }}"  autocomplete="token" autofocus placeholder="Token Node">

              @error('token')
                <span class="error invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>

        </div>
        
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>

      </form>

    </div>

  </div>
</div>

@endsection

@push('scripts')

<script>
  @if ($errors->any())

      @if (
        $errors->has('nama_node') ||
        $errors->has('ip_node') ||
        $errors->has('port_node') ||
        $errors->has('token_node')
      )
        $('#modal-add').modal('show'); 
      @else
        $('.modal-edit').modal('show'); 
      @endif
  @endif
</script>

@endpush