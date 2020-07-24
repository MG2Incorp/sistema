<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
        <link rel="stylesheet" href="{{ asset('css/megamenu.css') }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <style>
            .orange { color: orange !important }
            .bg-orange { background-color: orange !important }
            .unresize { resize: none !important }
            .pointer { cursor: pointer !important }
        </style>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <div id="app">
            <header id="header" class="u-header u-header--sticky-top">
                <div class="u-header__section">
                    <div id="logoAndNav" class="container">
                        <nav class="js-mega-menu navbar navbar-expand-md u-header__navbar u-header__navbar--no-space py-2">
                            <a class="navbar-brand u-header__navbar-brand-center" href="{{ route('index') }}"><img src="{{ asset('img/logo2.png') }}"></a>
                            <button type="button" class="navbar-toggler btn u-hamburger" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navBar" data-toggle="collapse" data-target="#navBar">
                                <span id="hamburgerTrigger" class="u-hamburger__box"><span class="u-hamburger__inner"></span></span>
                            </button>
                            <div id="navBar" class="collapse navbar-collapse">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item u-header__nav-item"><a id="goto_sobre" class="nav-link u-header__nav-link" href="#">Sobre</a></li>
                                    <li class="nav-item u-header__nav-item"><a id="goto_cases" class="nav-link u-header__nav-link" href="#">Cases</a></li>
                                    <li class="nav-item u-header__nav-item"><a id="goto_modulos" class="nav-link u-header__nav-link" href="#">Módulos</a></li>
                                    <li class="nav-item u-header__nav-item"><a id="goto_contratacao" class="nav-link u-header__nav-link" href="#">Contratação</a></li>
                                </ul>
                                <ul class="navbar-nav d-flex flex-row">
                                    <li class="nav-item u-header__nav-item"><a class="btn btn-sm btn-success transition-3d-hover pointer" tabindex="0" role="button" id="po_vendas">Vendas</a></li>
                                    <li class="nav-item u-header__nav-item"><a class="btn btn-sm btn-primary transition-3d-hover pointer ml-2" tabindex="0" role="button" id="po_clientes">Já é cliente?</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </header>

            <div id="SVGHero" class="svg-preloader position-relative bg-img-hero" style="background-image: url('{{ asset('img/bg-home2.png') }}');">
                <div class="container space-top-2 space-bottom-3 space-md-3 space-bottom-lg-4">
                    <div class="w-md-65 w-lg-50">
                        <h1 class="orange font-weight-medium">MG2 Incorp</h1>
                        <h2 class="orange">A solução <span class="text-white">completa</span><br> para seu <span class="text-white">empreendimento imobiliário.</h2>
                    </div>
                </div>
                <figure class="position-absolute right-0 bottom-0 left-0">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="none" x="0px" y="0px" width="100%" height="85px" viewBox="0 0 1920 107.7" style="margin-bottom: -8px; enable-background:new 0 0 1920 107.7;" xml:space="preserve" class="injected-svg js-svg-injector" data-parent="#SVGHero">
                        <style type="text/css">
                            .wave-9-bottom-0{fill:#FFFFFF;}
                            .wave-9-bottom-1{fill:#FFFFFF;}
                        </style>
                        <path class="wave-9-bottom-0 fill-white" d="M0,107.7V69.8c0,0,451-54.7,960-5.4S1920,0,1920,0v107.7H0z"></path>
                        <path class="wave-9-bottom-1 fill-white" opacity=".3" d="M0,107.7v-81c0,0,316.2-8.9,646.1,54.5s794.7-114.1,1273.9-38v64.5H0z"></path>
                    </svg>
                </figure>
            </div>

            <div id="sobre" class="container space-2">
                <div class="w-100 text-center mx-auto mb-7">
                    <h4 class="h5">Nós, da MG2 Incorp, trazemos um novo conceito de vendas para seu empreendimento,</h4>
                    <h4 class="h5 font-weight-normal">um sistema intuitivo e simples, onde a praticidade e a eficácia, contribui para melhorar sua organização empresarial.</h4>
                </div>
                <div class="position-relative w-100 bg-white text-center z-index-2 mx-lg-auto">
                    <ul class="nav nav-classic nav-rounded nav-shadow nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link font-weight-medium active" data-toggle="pill" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Gestão Comercial</a></li>
                        <li class="nav-item"><a class="nav-link font-weight-medium" data-toggle="pill" href="#tab2" role="tab" aria-controls="tab2" aria-selected="true">Gestão de Carteira</a></li>
                        <li class="nav-item"><a class="nav-link font-weight-medium" data-toggle="pill" href="#tab3" role="tab" aria-controls="tab3" aria-selected="true">CRM</a></li>
                        <li class="nav-item"><a class="nav-link font-weight-medium" data-toggle="pill" href="#tab4" role="tab" aria-controls="tab4" aria-selected="true">Completa</a></li>
                    </ul>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade pt-5 show active" id="tab1" role="tabpanel">
                        <div class="row justify-content-lg-center align-items-lg-center">
                            <div class="col-12 col-sm-6 col-md-4 mb-9 mb-lg-0">
                                <div class="mb-5">
                                    <h5 class="text-center">Desde a reserva, geração de propostas, análise de risco do proponente e emissão de contrato</h5>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="{{ asset('img/gestao_comercial3.png') }}" class="mw-100">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade pt-5" id="tab2" role="tabpanel">
                        <div class="row justify-content-lg-center align-items-lg-center">
                            <div class="col-12 col-sm-6 col-md-4 mb-9 mb-lg-0">
                                <div class="mb-5">
                                    <h5 class="text-center">Com geração e envio automático de boletos, 2ª via de boleto, cálculo de antecipação e quitação, reajustes automáticos, relatórios de inadimplentes e muito mais.</h5>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="{{ asset('img/gestao_financeira3.png') }}" class="mw-100">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade pt-5" id="tab3" role="tabpanel">
                        <div class="row justify-content-lg-center align-items-lg-center">
                            <div class="col-12 col-sm-6 col-md-4 mb-9 mb-lg-0">
                                <div class="mb-5">
                                    <h5 class="text-center">Através de nosso CRM tornamos a tarefa de acompanhamento de visitas e negociações se tornarem fáceis, além de possuirmos ferramentas para coleta automática de leads.</h5>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="{{ asset('img/gestao_crm3.png') }}" class="mw-100">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade pt-5" id="tab4" role="tabpanel">
                        <div class="row justify-content-lg-center align-items-lg-center">
                            <div class="col-12 col-sm-6 col-md-4 mb-9 mb-lg-0">
                                <div class="mb-5">
                                    <h5 class="text-center">Toda solução que seu empreendimento merece para ser um sucesso!</h5>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="{{ asset('img/gestao_completa3.png') }}" class="mw-100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row py-5">
                    <div class="col-12 d-flex justify-content-center">
                        <a class="btn btn-primary transition-3d-hover px-5 py-2 bg-orange border-0" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                    </div>
                </div>
            </div>

            <div id="cases" class="container">
                <div class="row">
                    <div class="col-lg-4 pt-7 pt-lg-0">
                        <div class="card border-0 bg-orange shadow-primary-lg p-5 p-lg-7">
                            <div class="text-white mb-7">
                                <span class="d-block font-size-1 font-weight-semi-bold text-uppercase mb-3">Junte-se aos nossos clientes MG2 Incorp</span>
                                <h2 class="h4">Temos o privilégio de trabalhar com excelentes clientes</h2>
                            </div>
                            <a class="btn btn-block btn-white transition-3d-hover" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                        </div>
                    </div>
                    <div class="col-lg-8 pt-7">
                        <div class="row text-center">
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/01.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/02.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/03.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/04.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/05.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/06.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/07.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/08.jpg') }}">
                            </div>
                            <div class="col-4 py-4">
                                <img class="u-clients" src="{{ asset('img/clients/09.jpg') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="position-relative">
                <div class="container space-2">
                    <div class="row align-items-center">
                        <div class="col-lg-5 mb-9 mb-lg-0">
                            <div class="pr-lg-4 mb-7">
                                <h2 class="orange">Entenda um pouco como nossa solução pode facilitar e otimizar a gestão de seu empreendimento</h2>
                            </div>
                        </div>
                        <div class="col-lg-7 position-relative">
                            <div id="youTubeVideoPlayer" class="u-video-player mb-5">
                                <a class="js-inline-video-player u-video-player__btn u-video-player__centered" href="javascript:;" data-video-id="vcuEYStDpMk" data-parent="youTubeVideoPlayer" data-is-autoplay="true" data-target="youTubeVideoIframe" data-classes="u-video-player__played">
                                    <span class="u-video-player__icon u-video-player__icon--lg text-primary"><span class="fas fa-play u-video-player__icon-inner"></span></span>
                                </a>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <div id="youTubeVideoIframe"></div>
                                </div>
                            </div>
                            <div id="SVGbgShape" class="w-100 content-centered-y z-index-n1">
                                <figure class="ie-soft-triangle-shape">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1109.8 797.1" style="enable-background:new 0 0 1109.8 797.1;" xml:space="preserve" class="injected-svg js-svg-injector" data-parent="#SVGbgShape">
                                        <style type="text/css"> .soft-triangle-shape-0{fill:#377DFF;} </style>
                                        <path class="soft-triangle-shape-0 fill-primary" opacity=".1" d="M105.1,267.1C35.5,331.5-3.5,423,0.3,517.7C6.1,663,111,831.9,588.3,790.8c753-64.7,481.3-358.3,440.4-398.3  c-4-3.9-7.9-7.9-11.7-12L761.9,104.8C639.4-27.6,432.5-35.6,299.9,87L105.1,267.1z"></path>
                                    </svg>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container space-2">
                <div class="w-lg-50 text-center mx-lg-auto mb-5">
                    <h2 class="font-weight-medium mb-4 orange">A gestão completa<br/> onde você estiver.</h2>
                    <p>Com o sistema MG2 Incorp se torna fácil gerir todos os processos de seu empreendimento, de forma simples e didática, com poucos cliques e o melhor de tudo de uma maneira segura e em qualquer local através de um Desktop, tablet ou celular.</p>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <a class="btn btn-primary transition-3d-hover px-5 py-2 bg-orange border-0" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                    </div>
                </div>
            </div>

            <div class="gradient-half-primary-v3">
                <div class="container space-top-2">
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-7">
                            <div class="text-center px-lg-3">
                                <span class="btn btn-icon btn-lg btn-soft-danger rounded-circle mb-5"><span class="fas fa-chart-line btn-icon__inner fa-2x btn-icon__inner-bottom-minus"></span></span>
                                <h3 class="h5">Painel do Cliente</h3>
                                <p class="mb-md-0">Um painel qual pode ser implantado no site de sua empresa, onde o cliente final consegue pelo CPF emitir 2 via de boletos, atualizar boletos vencidos e muito mais, minimizando assim o desgaste da equipe financeira.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-7">
                            <div class="text-center px-lg-3">
                                <span class="btn btn-icon btn-lg btn-soft-primary rounded-circle mb-5"><span class="fas fa-map btn-icon__inner fa-2x btn-icon__inner-bottom-minus"></span></span>
                                <h3 class="h5">Mapa Interativo</h3>
                                <p class="mb-md-0">No mapa interativo os corretores e clientes têm acesso a, disponibilidade e andamento das obras. Os mesmos são segmentados para empreendimentos horizontais ou verticais, sendo assim possível ver, por exemplo, a localização de um lote, ou a quantidade de apartamentos por andar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <figure>
                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="64px" viewBox="0 0 1921 273" style="margin-bottom: -8px; enable-background:new 0 0 1921 273;" xml:space="preserve">
                    <polygon class="fill-gray-100" points="0,273 1921,273 1921,0 "/>
                </svg>
            </figure>

            <div id="modulos" class="bg-light">
                <div class="container space-2">
                    <div class="w-100 text-center mx-md-auto mb-9">
                        <h2 class="font-weight-medium">Módulos</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 order-lg-2 mb-7 mb-lg-0">
                            <ul class="nav nav-box" role="tablist">
                                <li class="nav-item w-100 mx-0 mb-3">
                                    <a class="nav-link p-4 active" data-toggle="pill" href="#modulo1" role="tab" aria-controls="modulo1" aria-selected="true">
                                        <div class="media">
                                            <div class="media-body">
                                                <h3 class="h6 orange m-0">Gestão Comercial (Básico)</h3>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item w-100 mx-0 mb-3">
                                    <a class="nav-link p-4" data-toggle="pill" href="#modulo2" role="tab" aria-controls="modulo2" aria-selected="true">
                                        <div class="media">
                                            <div class="media-body">
                                                <h3 class="h6 orange m-0">Financeiro</h3>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item w-100 mx-0 mb-3">
                                    <a class="nav-link p-4" data-toggle="pill" href="#modulo3" role="tab" aria-controls="modulo3" aria-selected="true">
                                        <div class="media">
                                            <div class="media-body">
                                                <h3 class="h6 orange m-0">CRM</h3>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item w-100 mx-0 mb-3">
                                    <a class="nav-link p-4" data-toggle="pill" href="#modulo4" role="tab" aria-controls="modulo4" aria-selected="true">
                                        <div class="media">
                                            <div class="media-body">
                                                <h3 class="h6 orange m-0">Completa</h3>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-7 order-lg-1 align-self-lg-center">
                            <div class="tab-content pr-lg-4">
                                <div class="tab-pane fade show active" id="modulo1" role="tabpanel">
                                    <div class="row justify-content-center">
                                        <div class="col-12 col-sm-5">
                                            <img src="{{ asset('img/gestao_comercial3.png') }}" class="mw-100">
                                        </div>
                                        <div class="col-12 col-sm-7 d-flex flex-column justify-content-center">
                                            <ul>
                                                <li>Comercial</li>
                                                <li>Espelho de vendas</li>
                                                <li>Reserva de Unidades</li>
                                                <li>Status de reserva</li>
                                                <li>Mapa Interativo</li>
                                                <li>UpLoad de documentação</li>
                                                <li>Emissão de Contrato</li>
                                                <li>Tabela de Vendas</li>
                                                <li>Mensageiro Intranet</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="modulo2" role="tabpanel">
                                    <div class="row justify-content-center">
                                        <div class="col-12 col-sm-5">
                                            <img src="{{ asset('img/gestao_financeira3.png') }}" class="mw-100">
                                        </div>
                                        <div class="col-12 col-sm-7 d-flex flex-column justify-content-center">
                                            <ul>
                                                <li>Todas as funções da gestão comercial +</li>
                                                <li>Emissão automática de boletos com registro automática</li>
                                                <li>Envio automático de boletos por Email</li>
                                                <li>Envio automático de boletos por SMS</li>
                                                <li>Integração bancária por VAN (Value Added Network)</li>
                                                <li>Informe de imposto de renda (Envio automático)</li>
                                                <li>2 via de boletos</li>
                                                <li>Antecipação de parcelas</li>
                                                <li>Quitação de contratos</li>
                                                <li>Relatórios gerenciais</li>
                                                <li>Área do cliente</li>
                                                <li>E muito mais</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="modulo3" role="tabpanel">
                                    <div class="row justify-content-center">
                                        <div class="col-12 col-sm-5">
                                            <img src="{{ asset('img/gestao_crm3.png') }}" class="mw-100">
                                        </div>
                                        <div class="col-12 col-sm-7 d-flex flex-column justify-content-center">
                                            <ul>
                                                <li>Entrada multicanais</li>
                                                <li>Gerente de atendimento</li>
                                                <li>Transferência de atendimentos</li>
                                                <li>Histórico de atendimentos</li>
                                                <li>Portal de lançamento para plantões de vendas</li>
                                                <li>Mail Marketing</li>
                                                <li>SMS Marketing</li>
                                                <li>WhatsApp Marketing</li>
                                                <li>Relatórios</li>
                                                <li>E muito mais</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="modulo4" role="tabpanel">
                                    <div class="row justify-content-center">
                                        <div class="col-12 col-sm-5">
                                            <img src="{{ asset('img/gestao_completa3.png') }}" class="mw-100">
                                        </div>
                                        <div class="col-12 col-sm-7 d-flex flex-column justify-content-center">
                                            <ul>
                                                <li>Uma solução completa para atender seu empreendimento</li>
                                                <li>Havendo possibilidade de personalizações necessárias</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <figure>
                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="64px" viewBox="0 0 1921 273" style="margin-bottom: -8px; enable-background:new 0 0 1921 273;" xml:space="preserve">
                    <polygon class="fill-gray-100" points="1921,0 0,0 0,273 "/>
                </svg>
            </figure>

            <div id="contratacao" class="container">
                <div class="row align-items-lg-center">
                    <div id="SVGhouseAgency" class="col-lg-6">
                        <div class="pr-lg-7">
                            <div class="w-100 text-center mx-md-auto mb-9">
                                <span class="d-block text-secondary font-size-1 font-weight-medium text-uppercase mb-2">Conheça as etapas de contratação de nossas soluções para sua empresa</span>
                                <h2 class="font-weight-medium py-2">Entenda em <strong>três passos simples</strong> como vamos te atender:</h2>
                                <a class="btn btn-primary transition-3d-hover px-5 py-2 bg-orange border-0" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-7 mb-lg-0">
                        <ul class="list-unstyled">
                            <li class="u-indicator-steps py-3">
                                <div class="media align-items-center border rounded p-5">
                                    <div class="d-flex u-indicator-steps__inner mr-3"><span class="display-4 orange font-weight-medium">1.</span></div>
                                    <div class="media-body">
                                        <h3 class="h6">Agendamento de visita</h3>
                                        <p class="mb-0">Clique em &#8220;<a href="" data-toggle="modal" data-target="#modal_contratacao">ligamos para você</a>&#8221; e um executivo da unidade <strong>entrará em contato e agendará uma visita.</strong></p>
                                    </div>
                                </div>
                            </li>
                            <li class="u-indicator-steps py-3">
                                <div class="media align-items-center border rounded p-5">
                                    <div class="d-flex u-indicator-steps__inner mr-3"><span class="display-4 orange font-weight-medium">2.</span></div>
                                    <div class="media-body">
                                        <h3 class="h6">Entendimento do negócio</h3>
                                        <p class="mb-0">Em seguida você receberá um dos nossos arquitetos de solução para <strong>entendimento de suas necessidades e arquitetura da solução ideal para sua empresa</strong>.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="u-indicator-steps py-3">
                                <div class="media align-items-center border rounded p-5">
                                    <div class="d-flex u-indicator-steps__inner mr-3"><span class="display-4 orange font-weight-medium">3.</span></div>
                                    <div class="media-body">
                                        <h3 class="h6">Compra e implantação</h3>
                                        <p class="mb-0">Você então escolherá o <strong>melhor modelo de contratação, negociará valores</strong> e após o fechamento do contrato será iniciado o <strong>projeto de implantação.</strong></p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <footer class="container space-2">
                <hr class="my-7">
                <div class="row align-items-md-center">
                    <div class="col-12 col-md-3 mb-4 mb-sm-0">
                        <p class="small mb-0">Av 13, 213 – Sala 04 – Saúde<br>Rio Claro/SP<br>CEP 13.500-340</p>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-center">
                            <ul class="list-inline list-group-flush list-group-borderless text-md-center mb-0">
                                <li class="list-inline-item px-2">
                                    <img src="{{ asset('img/lgpd2.png') }}" alt="">
                                </li>
                                <li class="list-inline-item px-2">
                                    <img src="{{ asset('img/ssl2.png') }}" alt="">
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-4 mb-md-0">
                        <div class="text-center">
                            <a class="d-inline-flex align-items-center mb-2" href="{{ route('index') }}" aria-label="Front">
                                <span class="brand brand-primary"><img src="{{ asset('img/logo2.png') }}" alt=""></span>
                            </a>
                            <p class="small mb-0">© MG2 Incorp. {{ date('Y') }}. <br>Todos os direitos reservados.</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <div class="modal fade" id="modal_contratacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <form class="modal-content rounded-0" method="POST" action="/contato" id="form_contratacao">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Formulário para contato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body row justify-content-center">
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Nome Completo</label>
                                <input type="text" class="form-control form-control-sm" name="name" required placeholder="Ex: Alexandre Marinho">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Empresa</label>
                                <input type="text" class="form-control form-control-sm" name="company" required placeholder="Empresa X">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>E-Mail</label>
                                <input type="email" class="form-control form-control-sm" name="email" required placeholder="Ex: email@email.com.br">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Telefone / Celular</label>
                                <input type="text" class="form-control form-control-sm sp_celphones" name="phone" required placeholder="(19) 99999-9999">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Cargo na Empresa</label>
                                <input type="text" class="form-control form-control-sm" name="role" required placeholder="Ex: Gerente">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Cidade / UF</label>
                                <input type="text" class="form-control form-control-sm" name="local" required placeholder="Ex: São Paulo / SP">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Conte sua necessidade</label>
                                <textarea name="message" class="form-control form-control-sm unresize" rows="3" required placeholder="Ex: Gostaria de..."></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group" align="center">
                                <div class="g-recaptcha" data-sitekey="6LeRvr0UAAAAAKfQz8OGJjzOpgvs1BxY4OzopkVf"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center flex-row">
                        <button type="submit" class="btn btn-success px-5 py-2" id="btn_form_contratacao">Enviar</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('js/popper.js') }}?v={{ time() }}" defer></script>
        <script src="{{ asset('js/app2.js') }}?v={{ time() }}" defer></script>
        <script src="{{ asset('js/mask.js') }}" defer></script>
        <script src="{{ asset('js/validate.js') }}?v={{ time() }}" defer></script>

        <script src="{{ asset('js/svg.js') }}"></script>
        <script src="{{ asset('js/player.js') }}"></script>
        <script src="{{ asset('js/core.js') }}"></script>
        <script src="{{ asset('js/hs_svg.js') }}"></script>
        <script src="{{ asset('js/videoplayer.js') }}"></script>

        <script>
            $(window).on('load', function () {
                $.HSCore.components.HSSVGIngector.init('.js-svg-injector');
                $.HSCore.components.HSVideoPlayer.init('.js-inline-video-player');

                $(document).on('click', '#goto_sobre', function () { $('html,body').animate({ scrollTop: $('#sobre').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_cases', function () { $('html,body').animate({ scrollTop: $('#cases').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_modulos', function () { $('html,body').animate({ scrollTop: $('#modulos').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_contratacao', function () { $('html,body').animate({ scrollTop: $('#contratacao').offset().top-70 }, 1000); });
            });
            $(document).ready(function() {
                var SPMaskBehavior = function (val) { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; }, spOptions = { onKeyPress: function(val, e, field, options) { field.mask(SPMaskBehavior.apply({}, arguments), options); } };
                $('.sp_celphones').mask(SPMaskBehavior, spOptions);

                jQuery.extend(jQuery.validator.messages, { required: "Obrigatório.", email: "E-mail inválido." });

                jQuery.validator.setDefaults({
                    highlight: function(element) { $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); },
                    unhighlight: function(element) { $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid') },
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    submitHandler: function(form) { load(); form.submit(); }
                });

                $("#form_contratacao").validate({
                    submitHandler: function(form) {
                        form.submit();
                        $("#btn_form_contratacao").prop('disabled', true).html('<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>');
                    }
                });

                $(document).on('click', '.modal_contratacao', function() {
                    $('#po_vendas').popover('hide');
                    $("#modal_contratacao").modal('show');
                })

                $('#po_vendas').popover({
                    container: 'body', html: true, placement: 'bottom', trigger: 'focus',
                    content: '  <div class="p-2 text-center">\
                                    <h6>Ligue para:</h6>\
                                    <h5 class="lead">0800 042 0399</h5>\
                                    <h6>ou</h6>\
                                    <h5 class="lead"><i class="fab fa-whatsapp"></i> <a href="https://api.whatsapp.com/send?phone=551935008414" target="_BLANK">(19) 3500 8414</a></h5>\
                                    <h6>ou</h6>\
                                    <a class="btn btn-primary transition-3d-hover px-5 py-2 bg-orange border-0 modal_contratacao" href="#">Ligamos para você</a>\
                                </div>'
                });

                $('#po_clientes').popover({
                    container: 'body', html: true, placement: 'bottom', trigger: 'focus',
                    content: '  <div class="p-2 text-center">\
                                    <h6>Suporte</h6>\
                                    <h5 class="lead"><i class="fab fa-whatsapp"></i> <a href="https://api.whatsapp.com/send?phone=551935008414" target="_BLANK">(19) 3500 8414</a></h5>\
                                    <h6>ou</h6>\
                                    <a class="btn btn-primary transition-3d-hover px-5 py-2 bg-orange border-0" href="{{ route("home") }}" target="_BLANK">Acesse o painel</a>\
                                </div>'
                });
            });
        </script>
    </body>
</html>
