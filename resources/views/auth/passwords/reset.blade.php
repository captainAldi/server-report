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
          <a href="{{ route('awal') }}" class="h3"><b>Authentication</b></a>
          <br>
          <a href="{{ route('awal') }}" class="h3"></a>
        </div>
      </div>
      
    </div>
    <div class="card-body">
      <p class="login-box-msg">Reset Password</p>

      <form action="{{ route('password.update') }}" method="post">
        @csrf

          <input type="hidden" name="token" value="{{ $token }}">

         <div class="input-group mb-3">
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}"  autocomplete="email" placeholder="Email">
          
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
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password" placeholder="Password">
          
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

        <div class="input-group mb-3">
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password" placeholder="Retype password">
          
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>


          <!-- /.col -->
          <div class="col">
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
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
