<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($is_map))
    <meta http-equiv="refresh" content="120">
    @endif
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('css/app2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    @yield('css')
</head>

<body>
    @auth
    @if(!isset($hide))
    <div class="sliding-menu flex-center-wrapper flex-column left-menu">
        <div class="card bg-transparent">
            <div class="card-header d-flex justify-content-end"><span class="sliiider-exit exit left-exit text-white"><i class="fas fa-times"></i></span></div>
        </div>
        <div class="list-group border-0">
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('home') }}"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('proposals.index') }}"><i class="fas fa-signature"></i> Propostas</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('map.all') }}"><i class="fas fa-home"></i> Espelho de Vendas</a>

            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('companies.index') }}"><i class="fas fa-briefcase"></i> Imobiliárias</a>
            @endif

            @if(in_array(Auth::user()->role, ['ADMIN']))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('projects.index') }}"><i class="fas fa-home"></i> Empreendimentos</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('constructors.index') }}"><i class="fas fa-industry"></i> Incorporadoras</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract"></i> Contratos</a>
            @endif

            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'COORDINATOR']))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('users.index') }}"><i class="fas fa-users"></i> Usuários</a>
            @endif

            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('reports.payments') }}"><i class="fas fa-file-alt"></i> Relatório - Pagamentos</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('reports.billing') }}"><i class="fas fa-file-alt"></i> Relatório - Cobranças</a>
            @endif

            @if(Auth::user()->role == 'ADMIN' || (in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'ENGINEER'])))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('engineer.index') }}"><i class="fas fa-tractor"></i> Engenharia - Andamento das Obras</a>
            @endif

            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('billing.index') }}"><i class="fas fa-coins"></i> Financeiro</a></a>
            @endif

            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="https://form.jotformz.com/91977018010656" target="_BLANK"><i class="fas fa-file-invoice"></i> FAC</a>
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('messages.index') }}"><i class="fas fa-comment"></i> Mensagens</a>
            <!-- <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('users.password') }}"><i class="fas fa-lock"></i> Alterar senha</a> -->
            <a class="list-group-item py-1 white bg-transparent border-0 text-white" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-lock"></i> Sair</a>
        </div>
    </div>
    @endif
    @endauth
    <div id="app">
        @auth
        <div class="sticky-top d-print-none">
            @if(!isset($hide))
            <nav class="navbar navbar-expand-md navbar-light amber {{ isset($hide) ? 'shadow' : '' }}">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="{{ url('/') }}"><img src="{{ asset('img/logo2.png') }}"></a>
                    <button class="navbar-toggler" type="button" id="nav-icon2"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link text-white dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{ Auth::user()->name }} <span class="caret"></span></a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'COORDINATOR']))
                                    <a class="dropdown-item" href="{{ route('users.index') }}">Usuários</a>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['ADMIN']))
                                    <a class="dropdown-item" href="{{ route('settings') }}">Configurações</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('messages.index') }}">Mensagens</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            @endif
            @if(!isset($hide))
            <nav class="navbar navbar-expand-md navbar-light bg-light shadow d-none d-md-block">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('proposals.index') }}"><i class="fas fa-signature"></i> Propostas</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('map.all') }}"><i class="fas fa-home"></i> Espelho de Vendas</a></li>

                            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-hotel"></i> Implantação</a></a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('companies.index') }}">Imobiliárias</a>
                                    @if(in_array(Auth::user()->role, ['ADMIN']))
                                    <a class="dropdown-item" href="{{ route('projects.index') }}">Empreendimentos</a>
                                    <a class="dropdown-item" href="{{ route('owners.index') }}">Proprietários</a>
                                    <a class="dropdown-item" href="{{ route('constructors.index') }}">Incorporadoras</a>
                                    <a class="dropdown-item" href="{{ route('contracts.index') }}">Contratos</a>
                                    @endif
                                </div>
                            </li>
                            @endif

                            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-file-alt"></i> Relatórios</a></a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('reports.payments') }}">Pagamentos</a>
                                    <a class="dropdown-item" href="{{ route('reports.billing') }}">Cobranças</a>
                                </div>
                            </li>
                            @endif

                            @if(Auth::user()->role == 'ADMIN' || (in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'ENGINEER'])))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-tractor"></i> Engenharia</a></a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('engineer.index') }}">Andamento das Obras</a>
                                </div>
                            </li>
                            @endif

                            <li class="nav-item"><a class="nav-link" href="https://form.jotformz.com/91977018010656" target="_BLANK"><i class="fas fa-file-invoice"></i> FAC</a></li>

                            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-coins"></i> Financeiro</a></a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('billing.index') }}">Financeiro</a>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
            @endif
        </div>
        <main class="{{ Auth::check() && !isset($hide) ? 'py-4' : '' }}">
            @if(isset($breadcrumb))
            <div class="container-fluid d-print-none">
                <div class="row">
                    <div class="col-12">
                        <ol class="breadcrumb">
                            @foreach($breadcrumb as $bread)
                            @if($bread['is_link'])
                            <li class="breadcrumb-item"><a href="{{ $bread['link'] }}">{{ $bread['text'] }}</a></li>
                            @else
                            <li class="breadcrumb-item active" aria-current="page">{{ $bread['text'] }}</li>
                            @endif
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
            @endif
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
        @else
        @yield('content')
        @endif
    </div>

    <div class="h-100" id="b4_load" style="display: none">
        <div class="h-100 d-flex justify-content-center align-items-center">
            <div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/app2.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/slide.js') }}" defer></script>
    <script src="{{ asset('js/mask.js') }}" defer></script>
    <script src="{{ asset('js/money.js') }}" defer></script>
    <script src="{{ asset('js/validate.js') }}?v={{ time() }}" defer></script>

    <script>
        history.scrollRestoration = "manual";

        function load() {
            $("#app").toggle();
            $("#b4_load").toggle();
        }

        $(document).ready(function() {
            $(".cep").mask('00000-000');
            $(".telefone").mask('(00) 0000-0000');
            $(".celular").mask('(00) 0-0000-0000');
            $(".cpf").mask('000.000.000-00');
            $(".cnpj").mask('00.000.000/0000-00');
            $(".rg").mask('00.000.000-0');
            $('.money').maskMoney({
                thousands: '.',
                decimal: ',',
                allowZero: true
            });

            var $navIcon = $('#nav-icon2');
            var menu = $('.left-menu').sliiide({
                place: 'left',
                exit_selector: '.left-exit',
                toggle: '#nav-icon2',
                no_scroll: true
            });
            var notes = $('.note');
            var toggles = $('.slider-toggle');
            var clickHandler = function() {
                var $button = $(this);
                if ($button.hasClass('selected')) {
                    return;
                }
                $navIcon.removeClass('flip animated');
                notes.fadeOut(700);
                var place = $button.attr('data-link').split('-')[0];
                var menuPlace = $button.attr('data-link');
                var note;
                menu.reset();
                $('.sliding-menu').not('.' + menuPlace).addClass('display-off');
                $button.addClass('selected');
                $('.slider-toggle').not($button).removeClass('selected');
                menu = $('.' + menuPlace).sliiide({
                    place: place,
                    exit_selector: '.' + place + '-exit',
                    toggle: '#nav-icon2'
                });
                $navIcon.addClass('flip');
                $('.note[data-link="' + menuPlace + '"]').fadeIn(700).css('display', '').removeClass('display-off');
                $('.' + menuPlace).removeClass('display-off');
            }
            toggles.on('click', clickHandler);

            var SPMaskBehavior = function(val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.sp_celphones').mask(SPMaskBehavior, spOptions);

            // var doc_options = { onKeyPress: function (cpf, ev, el, op) { var masks = ['000.000.000-000', '00.000.000/0000-00']; $('.document_dyn').mask((cpf.length > 14) ? masks[1] : masks[0], op); } }
            // $('.document_dyn').length > 11 ? $('.document_dyn').mask('00.000.000/0000-00', doc_options) : $('.document_dyn').mask('000.000.000-00#', doc_options);

            $('.document_dyn').each(function(e) {
                var value = $(this).val().replace(/\D/g, '');
                var size = value.length;
                $(this).mask((size <= 11) ? '000.000.000-00' : '00.000.000/0000-00');
            })

            $(document).on('keydown', '.document_dyn', function(e) {
                var digit = e.key.replace(/\D/g, '');
                var value = $(this).val().replace(/\D/g, '');
                var size = value.concat(digit).length;
                $(this).mask((size <= 11) ? '000.000.000-00' : '00.000.000/0000-00');
            });

            jQuery.extend(jQuery.validator.messages, {
                required: "Obrigatório.",
                email: "E-mail inválido."
            });

            jQuery.validator.setDefaults({
                highlight: function(element) {
                    $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid')
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                submitHandler: function(form) {
                    load();
                    form.submit();
                }
            });
        })
    </script>

    @yield('js')
</body>

</html>