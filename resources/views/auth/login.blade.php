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
      <p class="login-box-msg">Login</p>

      <form action="{{ route('login') }}" method="post">
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

        <div class="input-group mb-3">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  placeholder="Password" autocomplete="current-password">

          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>

          @error('password')
            <span class="error invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              
              <label for="remember">
                Ingat Saya
              </label>
            </div>
          </div>

          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      @if(Route::has('password.request'))
        <p class="mb-1">
          <a href="{{ route('password.request') }}">Lupa Password</a>
        </p>
      @endif

      <!-- <p class="mb-0">
        <a href="{{ route('register') }}" class="text-center">Daftar</a>
      </p> -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
@endsection
