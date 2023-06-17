@extends('layouts.dashboard')

@section('content')

<div class="container">
    <div class="row">

        <!-- Data Semua Instance Server -->
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
                <div class="inner">
                <h3>{{ $count_all_instances }}</h3>

                <p>Running All Instance</p>
                </div>
                <div class="icon">
                <i class="fas fa-chart-pie"></i>
                </div>

                <a href="#" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                
            </div>
        </div>

        <!-- Data CE GCP -->
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-primary">
                <div class="inner">
                <h3>{{ $data_gcp_ce_running }}</h3>

                <p>Running Compute Engine</p>
                </div>
                <div class="icon">
                <i class="fas fa-chart-pie"></i>
                </div>

                <a href="{{ route('get.report.usage.gcp', ['cari_layanan' => 'Compute Engine']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                
            </div>
        </div>

        <!-- Data CE GCP -->
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-success">
                <div class="inner">
                <h3>{{ $data_gcp_csql_running }}</h3>

                <p>Running Cloud SQL</p>
                </div>
                <div class="icon">
                <i class="fas fa-chart-pie"></i>
                </div>

                <a href="{{ route('get.report.usage.gcp', ['cari_layanan' => 'Cloud SQL']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                
            </div>
        </div>

        <!-- Data VM Proxmox -->
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-warning">
                <div class="inner">
                <h3>{{ $data_proxmox_vm_running }}</h3>

                <p>Running Proxmox VM</p>
                </div>
                <div class="icon">
                <i class="fas fa-chart-pie"></i>
                </div>

                <a href="{{ route('get.report.usage.proxmox') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                
            </div>
        </div>

    
    </div>

    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
