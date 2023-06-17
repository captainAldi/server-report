
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets-adminlte3/dist/css/adminlte.min.css') }}">
  <!-- Custom style -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="{{ asset('assets-adminlte3/plugins/bs-stepper/css/bs-stepper.min.css') }}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('assets-adminlte3/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets-adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
  <!-- BS DatePicker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="hold-transition sidebar-mini layout-footer-fixed layout-navbar-fixed layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        @if(Auth::user()->role == "admin")
          <a href="{{ route('get.admin.home') }}" class="nav-link">Home</a>
        @elseif(Auth::user()->role == "staff")
          <a href="{{ route('get.staff.home') }}" class="nav-link">Home</a>
        @endif
      </li>

      <!-- Dark Mode Swithcer -->
      <li class="nav-item d-none d-sm-inline-block">
        <div class="theme-switch-wrapper nav-link">
          <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
            <span class="slider round"></span>
          </label>
        </div>
      </li>
      
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>
            @csrf
        </form>
        <a class="btn bg-info" 
            onclick="logoutUser()">
            Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
      <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('img/logo-putih.png') }}" alt="BSA Logo" class="brand-image elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Server Report</span>
      </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex center" style="justify-content: center;">
        <div class="info text-wrap">
          <a href="#" class="d-block">{{ auth()->user()->name }}</a>
        </div>
      </div>
      

      <!-- Sidebar Menu -->
      <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
            
            <li class="nav-item has-treeview {{ (request()->is('lokasi/*')) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ (request()->is('lokasi/*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-bookmark"></i>
                <p>
                  Lokasi
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="{{ route('get.lokasi.gcp') }}" class="nav-link {{ (request()->is('lokasi/gcp*')) ? 'active' : '' }}">
                    <i class="fa-brands fa-google nav-icon"></i>
                    <p>GCP</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('get.lokasi.proxmox') }}" class="nav-link {{ (request()->is('lokasi/proxmox*')) ? 'active' : '' }}">
                    <i class="fas fa-server nav-icon"></i>
                    <p>Proxmox</p>
                  </a>
                </li>

              </ul>
            </li>
            
            <li class="nav-item has-treeview {{ (request()->is('report/*')) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ (request()->is('report/*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Report
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                
                <li class="nav-item">
                  <a href="{{ route('get.report.usage.gcp') }}" class="nav-link {{ (request()->is('report/usage/gcp*')) ? 'active' : '' }}">
                    <i class="fa-brands fa-google nav-icon"></i>
                    <p>GCP</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('get.report.usage.proxmox') }}" class="nav-link {{ (request()->is('report/usage/proxmox*')) ? 'active' : '' }}">
                    <i class="fas fa-server nav-icon"></i>
                    <p>Proxmox</p>
                  </a>
                </li>
               
              </ul>
            </li>

            <li class="nav-item has-treeview {{ (request()->is('history/*')) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ (request()->is('history/*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-clock-rotate-left"></i>
                <p>
                  History
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                
                <li class="nav-item">
                  <a href="#" class="nav-link {{ (request()->is('report/usage/gcp*')) ? 'active' : '' }}">
                    <i class="fa-brands fa-google nav-icon"></i>
                    <p>GCP</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="#" class="nav-link {{ (request()->is('report/usage/proxmox*')) ? 'active' : '' }}">
                    <i class="fas fa-server nav-icon"></i>
                    <p>Proxmox</p>
                  </a>
                </li>
               
              </ul>
            </li>

          </ul>
        </nav>
      
      <!-- /.sidebar-menu-->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            @yield('header')
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              @if( !(request()->is('home')) )
                <li class="breadcrumb-item">
                    <a href="/">Home</a>
                </li>
                @yield('breadcrumb')
              @endif
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        @yield('content')
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Renaldi Y
    </div>
    <!-- Default to the left -->
    <strong>Copyright 2023 &copy;</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{ asset('assets-adminlte3/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('assets-adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets-adminlte3/dist/js/adminlte.min.js') }}"></script>
<!-- SweetAlert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<!-- Custom Made by Me -->
<script src="{{ asset('js/custom.js') }}"></script>
<!-- Toastr Flash -->
@if(session('kesalahan'))
  <script>
    toastr.error( '{{ session("kesalahan") }}' );
  </script> 
@elseif(session('pesan'))
  <script>
    toastr.success( '{{ session("pesan") }}' );
  </script> 
@endif

@stack('scripts')
</body>
</html>