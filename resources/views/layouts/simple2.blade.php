<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link href="{{ asset('css/app2.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        @yield('css')
        <style>
            html { font-size: 14px; }
            @media (min-width: 768px) { html { font-size: 16px; } }
            .pricing-header { max-width: 700px; }
            .flex-equal > * { -ms-flex: 1; flex: 1; }
            @media (min-width: 768px) { .flex-md-equal > * { -ms-flex: 1; flex: 1; } }
            .amber { background-color: #FFC107 !important; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top amber shadow">
            <div class="container">
                <a class="navbar-brand" href="#"><img src="{{ asset('img/logo2.png') }}"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link text-white" id="goto_sobre" href="#">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="goto_cases" href="#">Cases</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="goto_modulos" href="#">Módulos</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="goto_contratacao" href="#">Contratação</a></li>
                    </ul>
                    <button class="btn btn-primary my-2 my-sm-0 mr-2" type="submit">Vendas</button>
                    <button class="btn btn-success my-2 my-sm-0" type="submit">Já é nosso cliente?</button>
                </div>
            </div>
        </nav>

        <div class="pricing-header px-1 pb-0 pt-5 mt-5 mx-auto text-center">
            <h1 class="display-4">MG2 Incorp</h1>
            <h1 class="display-4">A solução completa para seu empreendimento imobiliário.</h1>
        </div>

        <div class="container">

            <div class="py-5" id="sobre">
                <div class="pricing-header px-3 pb-md-4 mx-auto text-center">
                    <p class="lead">Nós da MG2 Incorp, trazemos um novo conceito de vendas para seu empreendimento, um sistema intuitivo e simples, onde a praticidade e a eficacia, contribui para melhorar sua organização empresarial.</p>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 text-center py-2">
                            <div class="card-header font-weight-bold bg-transparent border-bottom-0 h5 pb-0">Gestão Comercial Completa</div>
                            <div class="card-body">
                                <p class="lead m-0">Desde a reserva, geração de propostas, análise de risco do proponente e emissão de contrato</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 text-center py-2">
                            <div class="card-header font-weight-bold bg-transparent border-bottom-0 h5 pb-0">Gestão de Carteira</div>
                            <div class="card-body">
                                <p class="lead m-0">Com geração e envio automático de boletos, 2ª via de boleto, cálculo de antecipação e quitação, reajustes automáticos, relatórios de inadimplentes e muito mais.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 text-center py-2">
                            <div class="card-header font-weight-bold bg-transparent border-bottom-0 h5 pb-0">Gerenciamento de Relacionamento com o Cliente</div>
                            <div class="card-body">
                                <p class="lead m-0">Através de nosso CRM tornamos a tarefa de acompanhamento de visitas e negociações se tornarem fáceis, além de possuirmos ferramentas para coleta automática de leads.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 text-center py-2">
                            <div class="card-header font-weight-bold bg-transparent border-bottom-0 h5 pb-0">Completa</div>
                            <div class="card-body">
                                <p class="lead m-0">Toda solução que seu empreendimento merece para ser um sucesso!</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 px-auto py-3 d-flex justify-content-center">
                        <a class="btn btn-info px-5 py-2" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                    </div>
                </div>
            </div>

            <div class="py-5" id="cases">
                <div class="pricing-header px-3 pb-3 mx-auto text-center">
                    <p class="lead">Junte-se aos nossos clientes MG2 Incorp</p>
                </div>

                <div class="row">
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4">
                        <div class="card h-100 text-center">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-5">
                <div class="pricing-header px-3 pb-3 mx-auto text-center">
                    <p class="lead">Entenda um pouco como nossa solução pode facilitar e otimizar a gestão de seu empreendimento.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8 mb-4">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-5">
                <div class="pricing-header px-3 pb-3 mx-auto text-center">
                    <h4 class="font-weight-bold">A GESTÃO COMPLETA ONDE VOCÊ ESTIVER</h4>
                    <p class="lead">Com o sistema MG2 Incorp se torna fácil gerir todos os processos de seu empreendimento, de forma simples e didática, com poucos cliques e o melhor de tudo de uma maneira segura e em qualquer local através de um Desktop, tablet ou celular.</p>
                </div>

                <div class="row">
                    <div class="col-12 px-auto d-flex justify-content-center">
                        <a class="btn btn-info px-5 py-2" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="container d-flex flex-column flex-md-row justify-content-between py-5">
            <div class="d-md-flex flex-md-equal w-100 my-md-3 pl-md-3">
                <div class="bg-dark mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5 pb-3">Painel do Cliente</h2>
                        <p class="lead m-0">Um painel qual pode ser implantado no site de sua empresa, qual o cliente pelo CPF consegue emitir 2 via de boletos, atualizar boletos vencidos e muito mais, minimizando assim o desgaste da equipe financeira.</p>
                    </div>
                </div>
                <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5 pb-3">Mapa Interativo</h2>
                        <p class="lead m-0">No mapa interativo os corretores e clientes têm acesso a, disponibilidade e andamento das obras. Os mesmos são segmentados para empreendimentos horizontais ou verticais, sendo assim possível ver, por exemplo, a localização de um lote, ou a quantidade de apartamentos por andar.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5" id="modulos">
            <div class="pricing-header px-3 pb-3 mx-auto text-center">
                <p class="lead">Módulos</p>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#basico" role="tab">Básico</a>
                            <a class="nav-link" data-toggle="tab" href="#crm" role="tab">CRM</a>
                            <a class="nav-link" data-toggle="tab" href="#financeiro" role="tab">Financeiro</a>
                            <a class="nav-link" data-toggle="tab" href="#personalizado" role="tab">Personalizado</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="basico" role="tabpanel">
                            <h2>basico</h2>
                            <p>tab1</p>
                        </div>
                        <div class="tab-pane fade" id="crm" role="tabpanel">
                            <h2>crm</h2>
                            <p>tab2</p>
                        </div>
                        <div class="tab-pane fade" id="financeiro" role="tabpanel">
                            <h2>financeiro</h2>
                            <p>tab3</p>
                        </div>
                        <div class="tab-pane fade" id="personalizado" role="tabpanel">
                            <h2>personalizado</h2>
                            <p>tab4</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5" id="contratacao">
            <div class="row featurette">
                <div class="col-md-5 mb-4">
                    <h2 class="featurette-heading">Conheça  as etapas de nossa contratação de nossas soluções para sua empresa.</span></h2>
                    <p class="lead">Entenda em três passos simples como vamos te atender:</p>
                    <a class="btn btn-info px-5 py-2" href="#" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                </div>
                <div class="col-md-7">
                    <div class="media mb-4">
                        <h1 class="mr-3 text-center" style="width: 3rem"><i class="far fa-calendar-alt"></i></h1>
                        <div class="media-body">
                            <h6>Passo 1</h6>
                            <h5 class="mt-0">Agendamento de visita</h5>
                            Clique em "ligamos para você" e um executivo da unidade TOTVS mais próxima <b>entrará em contato e agendará uma visita.</b>
                        </div>
                    </div>
                    <div class="media mb-4">
                        <h1 class="mr-3 text-center" style="width: 3rem"><i class="fas fa-hands-helping"></i></h1>
                        <div class="media-body">
                            <h6>Passo 2</h6>
                            <h5 class="mt-0">Entendimento do negócio</h5>
                            Em seguida você receberá um dos nossos arquitetos de solução para <b>entendimento de suas necessidades e arquitetura da solução ideal para sua empresa.</b>
                        </div>
                    </div>
                    <div class="media mb-4">
                        <h1 class="mr-3 text-center" style="width: 3rem"><i class="fas fa-chart-line"></i></h1>
                        <div class="media-body">
                            <h6>Passo 3</h6>
                            <h5 class="mt-0">Compra e implantação</h5>
                            Você então escolherá o <b>melhor modelo de contratação, negociará valores</b> e após o fechamento do contrato será iniciado o <b>projeto de implantação.</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <footer class="pt-4 my-md-5 pt-md-5 border-top">
                <div class="row">
                    <div class="col-12 col-md text-center text-sm-left">
                        <img src="{{ asset('img/logo2.png') }}" class="mb-2">
                        <small class="d-block mb-3 text-muted">&copy; 2017-2019</small>
                    </div>
                    <div class="col-12 col-md text-center text-sm-left">
                        <h5>Features</h5>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="#">Cool stuff</a></li>
                            <li><a class="text-muted" href="#">Random feature</a></li>
                            <li><a class="text-muted" href="#">Team feature</a></li>
                            <li><a class="text-muted" href="#">Stuff for developers</a></li>
                            <li><a class="text-muted" href="#">Another one</a></li>
                            <li><a class="text-muted" href="#">Last time</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-md text-center text-sm-left">
                        <h5>Resources</h5>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="#">Resource</a></li>
                            <li><a class="text-muted" href="#">Resource name</a></li>
                            <li><a class="text-muted" href="#">Another resource</a></li>
                            <li><a class="text-muted" href="#">Final resource</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-md text-center text-sm-left">
                        <h5>About</h5>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="#">Team</a></li>
                            <li><a class="text-muted" href="#">Locations</a></li>
                            <li><a class="text-muted" href="#">Privacy</a></li>
                            <li><a class="text-muted" href="#">Terms</a></li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>

        <div class="modal fade" id="modal_contratacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <form class="modal-content rounded-0">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Formulário para contato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Nome Completo</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Empresa</label>
                                <input type="text" class="form-control" name="company">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>E-Mail</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Telefone / Celular</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Cargo na Empresa</label>
                                <input type="text" class="form-control" name="role">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Cidade / UF</label>
                                <input type="text" class="form-control" name="local">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Conte sua necessidade</label>
                                <textarea name="message" class="form-control unresize" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center flex-row">
                        <button type="submit" class="btn btn-success px-5 py-2">Enviar</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('js/popper.js') }}?v={{ time() }}" defer></script>
        <script src="{{ asset('js/app2.js') }}?v={{ time() }}" defer></script>
        @yield('js')
        <script>
            history.scrollRestoration = "manual";

            $(document).ready(function() {
                $(document).on('click', '#goto_sobre', function () { $('html,body').animate({ scrollTop: $('#sobre').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_cases', function () { $('html,body').animate({ scrollTop: $('#cases').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_modulos', function () { $('html,body').animate({ scrollTop: $('#modulos').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_contratacao', function () { $('html,body').animate({ scrollTop: $('#contratacao').offset().top-70 }, 1000); });
            });
        </script>
    </body>
</html>
