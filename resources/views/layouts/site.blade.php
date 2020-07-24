<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">

        @yield('css')
    </head>
    <body>

        <div id="app">
            @if(!isset($hide))
                <nav class="navbar navbar-expand-md navbar-light amber">
                    <div class="{{ auth()->guard('client') ? 'container-fluid' : 'container' }}">
                        <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('img/logo2.png') }}"></a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}"><span class="navbar-toggler-icon"></span></button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto">
                                @guest('client')
                                    <li class="nav-item"><a class="nav-link" href="{{ route('client.login.show') }}">Entrar</a></li>
                                @else
                                    <li class="nav-item"><a class="nav-link" href="{{ route('client') }}">Meus Contratos</a></li>
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ auth()->guard('client')->user()->name }} <span class="caret"></span></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="{{ route('client.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair</a>
                                            <form id="logout-form" action="{{ route('client.logout') }}" method="POST" style="display: none;">@csrf</form>
                                        </div>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </nav>
            @endif
            <main class="py-4">
                @if(session('success'))
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    {{ session('error') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('js/popper.js') }}?v={{ time() }}" defer></script>
        <script src="{{ asset('js/app2.js') }}?v={{ time() }}" defer></script>

        @yield('js')
    </body>
</html>
