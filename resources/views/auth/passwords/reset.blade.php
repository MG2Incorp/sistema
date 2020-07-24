@extends('layouts.app')
@section('css')
    <style>
        .full-height { height: 100vh; }
        .flex-center { align-items: center; display: flex; justify-content: center;}
    </style>
@endsection
@section('content')
    <div class="full-height">
        <div class="container-fluid full-height">
            <div class="row full-height">
                <div class="col-12 col-sm-4">
                    <div class="row full-height flex-center justify-content-center">
                        <div class="col-12" align="center">
                            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}"></a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-8">
                    <div class="row full-height flex-center justify-content-center amber">
                        <div class="col-12 col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="form-group">
                                            <label>E-Mail</label>
                                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>Senha</label>
                                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('password') }}</strong></span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>Confirmar senha</label>
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" class="btn btn-outline-primary btn-block">Resetar senha</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
