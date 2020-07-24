
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <title>MG2 Incorp</title>

        <link rel='dns-prefetch' href='//fonts.googleapis.com'>
        <link rel="stylesheet" href="{{ asset('css/app2.css') }}?v={{ time() }}" type='text/css'>
        <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ time() }}" type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">
        <link rel="stylesheet" href='https://fonts.googleapis.com/css?family=Lato%3A300%2C400%2C500%2C700%2C900%7CRoboto%3A300%2C400%2C500%2C700&#038;ver=1.6.0' type='text/css' media='all' />


	    <!-- <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/style.css">
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/css/new-site/custom-bootstrap.css">
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/css/new-site/app.css">
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/css/new-site/intlTelInput.css"/>
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/css/new-site/request-demo.css">
        <link rel="stylesheet" type="text/css" href="https://vtex.com/wp-content/themes/VTEXTheme/css/new-site/platform-overview-tablet.css"> -->

        <style>
            .unresize { resize: none !important }
            /* .navbar-site {
                background: white !important;
            }
            .navbar-site.fixed-top {
                background: white;
                border-bottom: 1px solid rgba(0,0,0,.1);
                box-shadow: 2px 2px 12px rgba(0,0,0,.54);
            } */
            #page .carousel-item { min-height: 605px !important; }
            .page-home-header-title { text-transform: none !important }
            .orange { color: orange !important }
            .page-home .component-partners .fa-chevron-right { background-color: white !important; }

            .slick-next:before, .slick-prev:before {
                font-size: 30px !important;
                color: orange !important;
            }

            .slick-next {
                right: -40px !important;
            }

            .slick-prev {
                left: -40px !important;
            }
        </style>

        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body class="home page-template-default page page-id-513 page-home group-blog">
        <div class="hfeed site" id="page">
            <div class="wrapper-fluid wrapper-navbar" id="wrapper-navbar">
                <nav class="navbar navbar-expand-md navbar-site navbar-dark">
                    <div class="container">
                        <h1 class="navbar-brand mb-0">
                            <a rel="home" href="">
                                <img src="{{ asset('img/logo2.png') }}" class="logo">
                                <img src="{{ asset('img/logo2.png') }}" class="logo logo--white">
                            </a>
                        </h1>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                        <div id="navbarNavDropdown" class="collapse navbar-collapse">
                            <ul id="menu-header" class="navbar-nav">
                                <li class="menu-item menu-item-type-post_type menu-item-object-page nav-item menu-item-8091"><a id="goto_sobre" href="#" class="nav-link">Sobre</a></li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page nav-item menu-item-8091"><a id="goto_cases" href="#" class="nav-link">Cases</a></li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page nav-item menu-item-8091"><a id="goto_modulos" href="#" class="nav-link">Módulos</a></li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page nav-item menu-item-8091"><a id="goto_contratacao" href="#" class="nav-link">Contratação</a></li>
                                <li class="d-block d-md-none">
                                    <ul id="site-menu-mobile" class="navbar-nav">
                                        <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="" class="nav-link header-ctas btn btn-success">Vendas</a></li>
                                        <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="" class="nav-link header-ctas btn btn-info">Já é cliente?</a> </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div id="navbarForms" class="collapse navbar-collapse d-lg-flex d-md-none d-xl-none d-sm-none">
                            <ul id="site-menu" class="navbar-nav d-lg-flex d-md-none d-xl-flex d-sm-none">
                                <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="" class="nav-link header-ctas btn btn-success">Vendas</a></li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page ml-2"><a href="" class="nav-link header-ctas btn btn-info">Já é cliente?</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <section class="bg_header_color" id="sliderhome">
                <div id="myCarousel" class="carousel slide carousel-fade d-none d-md-block" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="background-image: url('{{ asset('img/bg-home.png') }}');">
                            <div class="container">
                                <div class="row col-12">
                                    <div class="padding_fix_slider">
                                        <div class="page-home-header-title">
                                            <h2 class="page-home-header-title" style="font-size: 45px;"><span class="color-white">MG2 Incorp,</span></h2>
                                            <h2 class="page-home-header-title" style="font-size: 35px;">A solução <span class="text-white">completa</span><br> para seu <span class="text-white">empreendimento imobiliário.</span></h2>
                                        </div>
                                    </div>
                                    <div class="carousel-content-buttom">
                                        <a href="" class="btn btn-accent btn-large slider_buttom_fix page-cta font-weight-bold respon_fix" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="myCarousel_mobile" class="carousel slide carousel-fade d-block d-md-none" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="background-image: url('{{ asset('img/bg.jpg') }}');">
                            <div class="container">
                                <div class="row col-12">
                                    <div class="padding_fix_slider">
                                        <div class="page-home-header-title">
                                            <h2 class="page-home-header-title" style="font-size: 45px;"><span class="color-white">MG2 Incorp,</span><br>A solução completa para seu negócio</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="page-home-solutions" id="sobre">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 text-center">
                            <h1><span class="orange">A</span> MG2 Incorp</h1>
                            <h3 class="text-center font-weight-light pb-5 pt-3">Nós da MG2 Incorp, trazemos um novo conceito de vendas para seu empreendimento, um sistema intuitivo e simples, onde a praticidade e a eficacia, contribui para melhorar sua organização empresarial.</h3>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-8">
                            <div class="center h-100">
                                <div>
                                    <div class="card mx-3" style="min-height: 200px">
                                        <div class="card-body text-center p-4">
                                            <h4 class="text-uppercase front-weight-bold pb-3">Gestão Comercial Completa</h4>
                                            <p>Desde a reserva, geração de propostas, análise de risco do proponente e emissão de contrato</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="card mx-3" style="min-height: 200px">
                                        <div class="card-body text-center p-4">
                                            <h4 class="text-uppercase front-weight-bold pb-3">Gestão de Carteira</h4>
                                            <p>Com geração e envio automático de boletos, 2ª via de boleto, cálculo de antecipação e quitação, reajustes automáticos, relatórios de inadimplentes e muito mais.</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="card mx-3" style="min-height: 200px">
                                        <div class="card-body text-center p-4">
                                            <h4 class="text-uppercase front-weight-bold pb-3">Gerenciamento de Relacionamento com o Cliente</h4>
                                            <p>Através de nosso CRM tornamos a tarefa de acompanhamento de visitas e negociações se tornarem fáceis, além de possuirmos ferramentas para coleta automática de leads.</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="card mx-3" style="min-height: 200px">
                                        <div class="card-body text-center p-4">
                                            <h4 class="text-uppercase front-weight-bold pb-3">Completa</h4>
                                            <p>Toda solução que seu empreendimento merece para ser um sucesso!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row box-solucoes">
                        <div class="col page-home-solutions-solution">
                            <div class="page-home-solutions-solution-box page-home-solutions-solution-box--experiencias" style="background-image:url('https://www.totvs.com/wp-content/uploads/2019/08/software-de-gestao-por-assinatura.jpg');">
                                <div class="page-home-solutions-solution-box-excerpt"><img class="mb-4" src="https://www.totvs.com/wp-content/uploads/2019/09/software-por-assinatura.png"/>Gestão Comercial Completa</div>
                                <div class="page-home-solutions-solution-box-content">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3 class="text-uppercase color-white front-weight-bold pb-3">Gestão Comercial Completa</h3>
                                            <div class="text-large pb-3">
                                                <p>Desde a reserva, geração de propostas, análise de risco do proponente e emissão de contrato</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col page-home-solutions-solution page-home-solutions-solution--active">
                            <div class="page-home-solutions-solution-box page-home-solutions-solution-box--ia" style="background-image:url('https://www.totvs.com/wp-content/uploads/2019/04/img-inteligencia.png');">
                                <div class="page-home-solutions-solution-box-excerpt"><img class="mb-4" src="https://www.totvs.com/wp-content/uploads/2019/04/AI_home.png"/>Gestão de Carteira</div>
                                <div class="page-home-solutions-solution-box-content">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3 class="text-uppercase color-white front-weight-bold pb-3">Gestão de Carteira</h3>
                                            <div class="text-large pb-3">
                                                <p>Com geração e envio automático de boletos, 2ª via de boleto, cálculo de antecipação e quitação, reajustes automáticos, relatórios de inadimplentes e muito mais.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col page-home-solutions-solution">
                            <div class="page-home-solutions-solution-box page-home-solutions-solution-box--experiencias" style="background-image:url('https://www.totvs.com/wp-content/uploads/2019/08/ecossistema-de-inovacao-a-sua-disposicao.jpg');">
                                <div class="page-home-solutions-solution-box-excerpt"><img class="mb-4" src="https://www.totvs.com/wp-content/uploads/2019/09/ecossistema-de-inovacao.png"/>Gerenciamento de Relacionamento com o Cliente</div>
                                <div class="page-home-solutions-solution-box-content">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3 class="text-uppercase color-white front-weight-bold pb-3">Gerenciamento de Relacionamento com o Cliente</h3>
                                            <div class="text-large pb-3">
                                                <p>Através de nosso CRM tornamos a tarefa de acompanhamento de visitas e negociações se tornarem fáceis, além de possuirmos ferramentas para coleta automática de leads.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col page-home-solutions-solution">
                            <div class="page-home-solutions-solution-box page-home-solutions-solution-box--experiencias" style="background-image:url('https://www.totvs.com/wp-content/uploads/2019/08/ecossistema-de-inovacao-a-sua-disposicao.jpg');">
                                <div class="page-home-solutions-solution-box-excerpt"><img class="mb-4" src="https://www.totvs.com/wp-content/uploads/2019/09/ecossistema-de-inovacao.png"/>Completa</div>
                                <div class="page-home-solutions-solution-box-content">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3 class="text-uppercase color-white front-weight-bold pb-3">Completa</h3>
                                            <div class="text-large pb-3">
                                                <p>Toda solução que seu empreendimento merece para ser um sucesso!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </section>

            <!-- <section class="component-partners" id="cases">
                <div class="container">
                    <div class="row">
                        <div class="col-md-5 pb-5 pb-md-0 d-flex align-items-center text-center text-md-left">
                            <h2 class="text-uppercase font-weight-bold text-center w-100">Junte-se ao nossos clientes MG2 Incorp</h2>
                        </div>
                        <div class="col-md-7 pl-md-5 component-partners-divider">
                            <i class="fa fa-chevron-right"></i>
                            <div class="row pt-5 align-items-center justify-content-center">
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-deca.png" alt="deca"/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/tres-logo.png" alt=""/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-cobasi.png" alt="cobasi"/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/therezopolis-logo.png" alt=""/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-azul.png" alt="azul"/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-kingstar.png" alt="kingstar"/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-harley-davidson.png" alt="harley-davidson"/></div>
                                <div class="col-md-3 col-6 mb-5 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-samsung.png" alt="samsung"/></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->

            <section class="page-home-intro">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 pb-5 text-center">
                            <h4 class="text-uppercase text-center color-secondary font-weight-bold">Junte-se ao nossos clientes MG2 Incorp</h4>
                            <div class="divider-accent w-25 mt-4 mb-3 mx-auto"></div>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-deca.png" alt="deca"/></div>
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/tres-logo.png" alt=""/></div>
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-cobasi.png" alt="cobasi"/></div>
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/therezopolis-logo.png" alt=""/></div>
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-azul.png" alt="azul"/></div>
                        <div class="col-md-2 col-6 mb-1 text-center"><img class="img-grayscale" src="https://www.totvs.com/wp-content/uploads/2019/08/logo-kingstar.png" alt="kingstar"/></div>
                    </div>
                </div>
            </section>

            <section class="component-partners" id="cases">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 pb-5 pb-md-0 d-flex align-items-center text-center text-md-left">
                            <h2 class="text-uppercase font-weight-bold text-center w-100 orange">Entenda um pouco como nossa solução pode facilitar e otimizar a gestão de seu empreendimento</h2>
                        </div>
                        <div class="col-md-6 pl-md-5 component-partners-divider">
                            <i class="fa fa-chevron-right"></i>
                            <div class="row pt-5 align-items-center justify-content-center">
                                <div class="col-12 mb-5 text-center">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe width="730" height="411" src="https://www.youtube.com/embed/JlEspIhf7Dk?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section class="page-home-intro">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 pb-5 text-center">
                            <h4 class="text-uppercase text-center color-secondary font-weight-bold">Entenda um pouco como nossa solução pode facilitar e otimizar a gestão de seu empreendimento</h4>
                            <div class="divider-accent w-50 mt-4 mb-3 mx-auto"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="embed-responsive embed-responsive-16by9">
                                <p><iframe width="730" height="411" src="https://www.youtube.com/embed/JlEspIhf7Dk?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->

            <section class="component-segments">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <h2 class="text-uppercase text-center font-weight-bold">A gestão completa<br/> onde <span class="color-accent">você estiver.</span></h2>
                            <div class="divider-accent w-50 mt-4 mb-5 aligncenter"></div>
                            <h4>Com o sistema MG2 Incorp se torna fácil gerir todos os processos de seu empreendimento, de forma simples e didática, com poucos cliques e o melhor de tudo de uma maneira segura e em qualquer local através de um Desktop, tablet ou celular.</h4>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section class="page-home-globe" style="background-image: url('https://www.totvs.com/wp-content/uploads/2019/04/bg-globe.png');"> -->
            <section class="page-home-globe">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-4 pb-5">
                            <h3 class="text-uppercase text-center color-secondary font-weight-bold pb-5 slider_desafio_ajuste">Painel do Cliente</h3>
                            <div class="text-large text-justify">
                                <p><span style="font-weight: 400;">Um painel qual pode ser implantado no site de sua empresa, qual o cliente pelo CPF consegue emitir 2 via de boletos, atualizar boletos vencidos e muito mais, minimizando assim o desgaste da equipe financeira.</span></p>
                            </div>
                            <!-- <img class="page-home-globe-line d-none d-sm-block" src="https://www.totvs.com/wp-content/themes/totvs-site/img/home/line-5.png"/> -->
                        </div>
                        <div class="col-md-4 offset-md-1">
                            <h3 class="text-uppercase text-center color-secondary font-weight-bold pb-5 slider_desafio_ajuste">Mapa Interativo</h3>
                            <div class="text-large text-justify">
                                <p><span style="font-weight: 400;">No mapa interativo os corretores e clientes têm acesso a, disponibilidade e andamento das obras. Os mesmos são segmentados para empreendimentos horizontais ou verticais, sendo assim possível ver, por exemplo, a localização de um lote, ou a quantidade de apartamentos por andar.</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section class="page-bi-features pt-5" id="modulos">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <h2 class="font-weight-bold text-center text-uppercase">Módulos</h2>
                            <div class="divider-primary w-25 mt-4 mb-5 aligncenter"></div>
                        </div>
                    </div>
                </div>
                <div class="page-bi-features-browse mt-5 mb-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="page-erp-features-browse-list">
                                    <li class="page-rh-features-browse-list-item d-content-item d-content-item--active" data-target="integracao-de-dados">Básico</li>
                                    <li class="page-rh-features-browse-list-item d-content-item" data-target="big-data">CRM</li>
                                    <li class="page-rh-features-browse-list-item d-content-item" data-target="servicos-de-processamento">Financeiro</li>
                                    <li class="page-rh-features-browse-list-item d-content-item" data-target="entrega-de-insights">Personalizado</li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="page-rh-features-browse-content d-content d-content--integracao-de-dados">
                                    <h2>Conteúdo Básico</h2>
                                </div>
                                <div class="page-rh-features-browse-content d-none d-content d-content--big-data">
                                    <h2>Conteúdo CRM</h2>
                                </div>
                                <div class="page-rh-features-browse-content d-none d-content d-content--servicos-de-processamento">
                                    <h2>Conteúdo Financeiro</h2>
                                </div>
                                <div class="page-rh-features-browse-content d-none d-content d-content--entrega-de-insights">
                                    <h2>Conteúdo Personalizado</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->

            <section id="solutions" class="pt-3 pt-md-2 pt-lg-5 border-top w-100">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <p class="text-secondary">O que fazemos de melhor</p>
                            <h1 class="display-2 text-secondary mb-0">Uma única solução para</h1>
                        </div>
                    </div>
                    <div class="row pt-2 pt-md-3 pt-lg-4">
                        <div class="col-md-6 col-lg-5 list-info">
                            <div class="container-fluid active" data-index="1">
                                <div class="row">
                                    <div class="col-1 index">
                                        <p class="text-muted lead font-weight-bold mb-0">01</p>
                                    </div>
                                    <div class="col-10 col-lg-11 title">
                                        <p class="text-muted lead mb-0">OMS Unificado</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1" style="padding-left: 26px;padding-right: 0;">
                                        <div class="progress progress-bar-vertical mt-h mb-h">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="30"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">30% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10 col-lg-11 pt-h pb-s content">
                                        <p class="text-muted font-weight-light mb-0 small">Acompanhe todos os pedidos em tempo real e otimize os custos de seus canais de venda, logística e fechamento de pedido.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid " data-index="2">
                                <div class="row">
                                    <div class="col-1 index">
                                        <p class="text-muted lead font-weight-bold mb-0">02</p>
                                    </div>
                                    <div class="col-10 col-lg-11 title">
                                        <p class="text-muted lead mb-0">Checkout web e inStore integrados</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1" style="padding-left: 26px;padding-right: 0;">
                                        <div class="progress progress-bar-vertical mt-h mb-h">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="30"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">30% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10 col-lg-11 pt-h pb-s content">
                                        <p class="text-muted font-weight-light mb-0 small">Conecte suas lojas físicas com dados do e-commerce para criar experiências de compra consistentes, oferecer promoções personalizadas e melhorar suas vendas.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid " data-index="3">
                                <div class="row">
                                    <div class="col-1 index">
                                        <p class="text-muted lead font-weight-bold mb-0">03</p>
                                    </div>
                                    <div class="col-10 col-lg-11 title">
                                        <p class="text-muted lead mb-0">Gerenciador de experiências</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1" style="padding-left: 26px;padding-right: 0;">
                                        <div class="progress progress-bar-vertical mt-h mb-h">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="30"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">30% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10 col-lg-11 pt-h pb-s content">
                                        <p class="text-muted font-weight-light mb-0 small">Das clusterizações ao layout, passando pelo catálogo, crie experiências contextuais em todos os seus canais de venda.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid " data-index="4">
                                <div class="row">
                                    <div class="col-1 index">
                                        <p class="text-muted lead font-weight-bold mb-0">04</p>
                                    </div>
                                    <div class="col-10 col-lg-11 title">
                                        <p class="text-muted lead mb-0">Serviço e atendimento ao cliente</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1" style="padding-left: 26px;padding-right: 0;">
                                        <div class="progress progress-bar-vertical mt-h mb-h">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="30"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">30% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10 col-lg-11 pt-h pb-s content">
                                        <p class="text-muted font-weight-light mb-0 small">De assinaturas a processos simples de alteração de pedido, dê poder aos seus clientes e coloque-os no centro das suas operações.</p>
                                    </div>
                                </div>
                            </div>
                            <a href="https://www.vtex.com/pt-br/overview-plataforma" id="buttonHome" class="btn btn-primary text-uppercase font-weight-light mb-2 mb-lg-5 border-0 d-block d-md-table mt-s rounded">TENHA UMA VISÃO GERAL DA PLATAFORMA</a>
                        </div>
                        <div class="col-md-6 col-lg-7 list-img d-none d-md-block">
                            <img class="img-fluid active" src="https://vtex.com/wp-content/uploads/2019/09/Order-Management-1.png" alt="" data-index="1">
                            <img class="img-fluid " src="https://vtex.com/wp-content/uploads/2019/09/Web-and-inStore-Point-of-Commerce-2.png" alt="" data-index="2">
                            <img class="img-fluid " src="https://vtex.com/wp-content/uploads/2019/09/Context-driven- Experience-Management-3.png" alt="" data-index="3">
                            <img class="img-fluid " src="https://vtex.com/wp-content/uploads/2019/09/Customer-service- and-clienteling-4.png" alt="" data-index="4">
                        </div>
                    </div>
                </div>
            </section>

            <section class="page-manufatura-step-by-step pt-5" id="contratacao">
                <div class="container mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-5 offset-md-1 text-center text-md-right pb-5">
                            <div class="pr-md-5">
                                <h6 class="color-primary text-uppercase">PASSO A PASSO</h6>
                                <h2 class="color-secondary">Conheça as etapas de contratação de nossas soluções para sua empresa.</h2>
                                <h5 class="font-weight-light mt-5">Entenda em <strong>três passos simples</strong> como vamos te atender:</h5>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="row mb-4">
                                <div class="col-md-3 pb-4 text-center">
                                    <img src="https://www.totvs.com/wp-content/uploads/2019/04/icon-calendar.png"/>
                                </div>
                                <div class="col col-md-9 text-center text-md-left">
                                    <span class="color-accent">Passo 1</span>
                                    <h5 class="color-primary">Agendamento de visita</h5>
                                    <p>Clique em &#8220;<u>ligamos para você</u>&#8221; e um executivo da unidade TOTVS mais próxima <strong>entrará em contato e agendará uma visita.</strong></p>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 pb-4 text-center">
                                    <img src="https://www.totvs.com/wp-content/uploads/2019/04/icon-talk.png"/>
                                </div>
                                <div class="col col-md-9 text-center text-md-left">
                                    <span class="color-accent">Passo 2</span>
                                    <h5 class="color-primary">Entendimento do negócio</h5>
                                    <p>Em seguida você receberá um dos nossos arquitetos de solução para <strong>entendimento de suas necessidades e arquitetura da solução ideal para sua empresa</strong>.</p>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 pb-4 text-center">
                                    <img src="https://www.totvs.com/wp-content/uploads/2019/04/icon-analytics.png"/>
                                </div>
                                <div class="col col-md-9 text-center text-md-left">
                                    <span class="color-accent">Passo 3</span>
                                    <h5 class="color-primary">Compra e implantação</h5>
                                    <p>Você então escolherá o <strong>melhor modelo de contratação, negociará valores</strong> e após o fechamento do contrato será iniciado o <strong>projeto de implantação.</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <footer class="site-footer wrapper site-footer--home" id="wrapper-footer" style="background-image: url('https://www.totvs.com/wp-content/uploads/2019/05/bg-footer.png');">
                <div class="container">
                    <div class="page-home-footer">
                        <div class="row">
                            <div class="col-md-12 d-none d-sm-block">
                                <div class="page-home-footer-subtitle">Para o sucesso do seu negócio,</div>
                                <div class="page-home-footer-title">
                                    Conte com
                                    <div class="pl-5">as soluções</div>
                                    <div class="page-home-footer-title-totvs">MG2 Incorp
                                        <img src="https://www.totvs.com/wp-content/themes/totvs-site/img/home/lines.png">
                                        <a href="" class="page-home-footer-title-totvs-cta" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 d-block d-sm-none">
                                <div class="page-home-footer-subtitle">Para o sucesso do seu negócio,</div>
                                <div class="page-home-footer-title pt-2">
                                    Conte com a MG2 Incorp
                                    <img src="https://www.totvs.com/wp-content/themes/totvs-site/img/home/lines.png" class="d-none d-sm-block" alt="">
                                    <div class="text-center pt-5">
                                        <a href="" class="btn-accent page-cta" data-toggle="modal" data-target="#modal_contratacao">Ligamos para você</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-3 text-center text-md-left pb-5 pb-md-0">
                                <a rel="home" href=""><img src="{{ asset('img/logo2.png') }}"></a>
                            </div>
                            <div class="col-md-3 text-center text-md-right">
                                <h3 class="color-accent text-uppercase font-weight-bold">Fale Conosco</h3>
                                <h3 class="color-white text-uppercase font-weight-bold">0800 70 98 100</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </footer> -->
            <div class="container">
                <hr>
                <div class="row py-4 justify-content-center">
                    <div class="col-md-3 text-center text-md-left pb-5 pb-md-0">
                        <a rel="home" href=""><img src="{{ asset('img/logo2.png') }}"></a>
                    </div>
                    <div class="col-md-3 text-center text-md-right">
                        <h5 class="color-accent text-uppercase font-weight-bold">Fale Conosco</h5>
                        <h5 class="color-white text-uppercase font-weight-bold">0800 70 98 100</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_contratacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <form class="modal-content rounded-0" method="POST" action="{{ action('IndexController@contact') }}">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Formulário para contato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body row justify-content-center">
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Nome Completo</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Empresa</label>
                                <input type="text" class="form-control" name="company" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>E-Mail</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Telefone / Celular</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Cargo na Empresa</label>
                                <input type="text" class="form-control" name="role" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Cidade / UF</label>
                                <input type="text" class="form-control" name="local" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Conte sua necessidade</label>
                                <textarea name="message" class="form-control unresize" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group" align="center">
                                <div class="g-recaptcha" data-sitekey="6LeRvr0UAAAAAKfQz8OGJjzOpgvs1BxY4OzopkVf"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center flex-row">
                        <button type="submit" class="btn btn-success px-5 py-2">Enviar</button>
                    </div>
                </form>
            </div>
        </div>

        <script type='text/javascript' src='https://www.totvs.com/wp-content/themes/totvs-site/js/theme.min.js?ver=1.6.0'></script>
        <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js'></script>

        <script type="text/javascript">
            history.scrollRestoration = "manual";
            $(document).ready(function() {
                var contClick = false;
                $('.navbar-toggler').click(function() {
                    if(contClick == false){
                        $('.page-home-header-title').addClass('paddingfixSlider1');
                        $('.sec_slider_white').addClass('paddingfixSlider2');
                        $('.carousel-content_slider_video').addClass('paddingfixSlider3');
                        $('.page-header_bg').addClass('paddingfixPages');
                        contClick = true;
                    } else {
                        $('.page-home-header-title').removeClass('paddingfixSlider1');
                        $('.sec_slider_white').removeClass('paddingfixSlider2');
                        $('.carousel-content_slider_video').removeClass('paddingfixSlider3');
                        $('.page-header_bg').removeClass('paddingfixPages');
                        contClick = false;
                    }
                });

                $(document).on('click', '#goto_sobre', function () { $('html,body').animate({ scrollTop: $('#sobre').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_cases', function () { $('html,body').animate({ scrollTop: $('#cases').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_modulos', function () { $('html,body').animate({ scrollTop: $('#modulos').offset().top-70 }, 1000); });
                $(document).on('click', '#goto_contratacao', function () { $('html,body').animate({ scrollTop: $('#contratacao').offset().top-70 }, 1000); });

                $('.center').slick({
                    centerMode: true,
                    centerPadding: '60px',
                    slidesToShow: 1,
                    responsive: [
                        {
                        breakpoint: 768,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '40px',
                            slidesToShow: 1
                        }
                        },
                        {
                        breakpoint: 480,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '40px',
                            slidesToShow: 1
                        }
                        }
                    ]
                });

                var solutions = $('#solutions .list-info .title p, #solutions .list-info .index p');
                var z = 0;
                var delay = 3000;

                solutions.each(function () {
                    var index = $(this).closest('.container-fluid').data('index');
                    var img = $('.list-img img');

                    $(this).click(function () {
                        $(this).closest('.container-fluid').addClass('active');
                        $(this).closest('.container-fluid').siblings().removeClass('active');
                        $(this).closest('.progress-bar-vertical').css('transform', 'rotate(0deg)');

                        if (img.hasClass('active')) {
                            img.removeClass('active');
                        }

                        img.each(function () {
                            if (index == $(this).data('index')) {
                                $(this).addClass('active');
                            }
                        });

                        z = index;
                    });
                });

                function loopSolutions() {
                    var index = solutions.last().closest('.container-fluid').data('index');
                    if (++z <= index) {
                        setTimeout(function () {
                            solutions.each(function () {
                                var data = $(this).closest('.container-fluid').data('index');
                                var img = $('.list-img img');

                                if (data == z) {
                                    $(this).closest('.container-fluid').addClass('active');
                                    $(this).closest('.container-fluid').siblings().removeClass('active');

                                    if (img.hasClass('active')) {
                                        img.removeClass('active');
                                    }

                                    img.each(function () {
                                        if (data == $(this).data('index')) {
                                            $(this).addClass('active');
                                        }
                                    });
                                }
                            });
                            loopSolutions();
                        }, delay);
                    } else {
                        z = 0;
                        loopSolutions();
                    }
                }
                if ($('#solutions').length > 0) {
                    loopSolutions();
                }
            });
        </script>
    </body>
</html>