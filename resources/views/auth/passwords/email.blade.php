@extends('layouts.auth')

@section('content')

<div class="mb-5">
  <img src="{{ asset('img/logo.png') }}"  alt="BSA Logo" class="brand-image" style="max-width: 100%; height: auto;">
</div>

<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      
      <div class="row">
        <div class="col">
          <a href="{{ route('awal') }}" class="h3"><b>BSA</b></a>
          <br>
          <a href="{{ route('awal') }}" class="h3">Server Report</a>
        </div>
      </div>

    </div>
    <div class="card-body">
      <p class="login-box-msg">Email Reset Password</p>

      <form action="{{ route('password.email') }}" method="post">
        @csrf

        <div class="input-group mb-3">
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" placeholder="Email" autofocus>

          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>

          @error('email')
            <span class="error invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>


          <!-- /.col -->
          <div class="col">
            <button type="submit" class="btn btn-primary btn-block">Kirim Link Reset Password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
@endsection
