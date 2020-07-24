@extends('layouts.site')
@section('content')
    <div class="container space-1">
        <form class="js-validate w-md-75 w-lg-50 mx-md-auto" method="POST" action="{{ route('client.login') }}">
            @csrf
            <div class="card shadow">
                <div class="card-body p-7">
                    <div class="mb-5 text-center">
                        <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('img/logo2.png') }}"></a>
                        <h2 class="h3 text-primary font-weight-normal mb-0">Bem vindo <span class="font-weight-semi-bold">novamente!</span></h2>
                        <p>Entre para gerenciar sua conta</p>
                    </div>
                    <div class="js-form-message form-group">
                        <label class="form-label">CPF (Somente números)</label>
                        <input id="cpf" type="text" class="form-control{{ $errors->has('cpf') ? ' is-invalid' : '' }}" name="cpf" value="{{ old('cpf') }}" required autofocus>
                        @if($errors->has('cpf')) <span class="invalid-feedback"><strong>{{ $errors->first('cpf') }}</strong></span> @endif
                    </div>
                    <div class="js-form-message form-group">
                        <label class="form-label"><span class="d-flex justify-content-between align-items-center">Senha</label>
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        @if($errors->has('password')) <span class="invalid-feedback"><strong>{{ $errors->first('password') }}</strong></span> @endif
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-6">
                            <button type="submit" class="btn btn-primary btn-block transition-3d-hover">Entrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection