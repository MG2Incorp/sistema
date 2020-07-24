<?php

// $appRoutes = function () {
// Route::get('/',                                 'HomeController@index')         ->name('home')->middleware('message');
// Route::get('/site',                             'IndexController@index')        ->name('index');
Route::get('/',                                 'IndexController@index')->name('index');
// Route::get('/home',                             'HomeController@index')         ->name('home')->middleware('message');
Route::post('/contato',                         'IndexController@contact')->name('contact');
Route::get('/validacao',                        'IndexController@validation')->name('validation');
Route::get('/simulador',                        'IndexController@simulator')->name('simulator');
Route::get('/financeiro/boleto/visualizacao',   'BillingController@billet')->name('billing.billet');
Route::get('/mapa/{slug}',                      'IndexController@map')->name('map');
Route::get('/empreendimento/{slug}',            'ProjectController@lead')->name('lead');
Route::post('/empreendimento/lead/salvar',      'ProjectController@lead_store')->name('lead.store');
// };

Route::domain('cliente.mg2incorp.com.br')->group(function () {
    // Route::prefix('cliente')->group(function () {
    Route::get('/',         'ClientController@index')->name('client');
    Route::get('/contrato', 'ClientController@contract')->name('client.contract');

    Route::get('login',     'Auth\ClientLoginController@showLoginForm')->name('client.login.show');
    Route::post('login',    'Auth\ClientLoginController@login')->name('client.login');
    Route::post('logout',   'Auth\ClientLoginController@logout')->name('client.logout');

    Route::group(['prefix' => 'financeiro'], function () {
        Route::post('/cobranca/detalhes',           'BillingController@billing')->name('client.billing.billing');
        Route::post('/antecipacao/valores',         'BillingController@ahead_value')->name('client.billing.ahead.value');
        Route::post('/antecipacao/pagamento',       'BillingController@ahead_payment')->name('client.billing.ahead.payment');
        Route::post('/antecipacao/total',           'BillingController@ahead_total_value')->name('client.billing.ahead.total.value');
        Route::post('/antecipacao/total/gerar',     'BillingController@ahead_total_generate')->name('client.billing.ahead.total.generate');
    });
    // });
});

// Route::prefix('cliente')->group(function () {

// Route::domain('portal.mg2incorp.com.br')->group(function () {
Route::group(['prefix' => 'portal'], function () { // Rota adicionada


    Auth::routes(['register' => false]);

    Route::get('/testes',         'TestController@index')->name('testes');

    Route::get('/',                                 'HomeController@index')->name('home')->middleware('message');
    Route::get('/home',                             'HomeController@index')->name('home')->middleware('message');

    Route::group(['middleware' => ['auth']], function () {
        //Route::get('/alterar-senha', 'UserController@password')->name('users.password')->middleware('message');

        Route::post('/notificacao/fechar',  'HomeController@notification')->name('notification.close');

        Route::match(['get', 'post'], '/alterar-senha', 'UserController@password')->name('users.password')->middleware('message');

        Route::group(['prefix' => 'imobiliarias', 'middleware' => ['is_admin_incorp']], function () {
            Route::get('/', 'CompanyController@index')->name('companies.index')->middleware('message');
            Route::get('/criar', 'CompanyController@create')->name('companies.create')->middleware('message');
            Route::post('/salvar', 'CompanyController@store')->name('companies.store');
            Route::get('/visualizar/{id}', 'CompanyController@show')->name('companies.show')->middleware('message');
            Route::get('/editar/{id}', 'CompanyController@edit')->name('companies.edit')->middleware('message');
            Route::post('/atualizar/{id}', 'CompanyController@update')->name('companies.update');
            Route::get('/deletar/{id}', 'CompanyController@delete')->name('companies.delete')->middleware('message');
        });

        Route::group(['prefix' => 'empreendimentos'], function () {
            Route::group(['middleware' => ['is_admin']], function () {
                Route::get('/',                 'ProjectController@index')->name('projects.index')->middleware('message');
                Route::get('/criar',            'ProjectController@create')->name('projects.create')->middleware('message');
                Route::post('/salvar',          'ProjectController@store')->name('projects.store');
                Route::post('/salvar2',         'TestController@store2')->name('projects.store2');
                Route::get('/editar/{id}',      'ProjectController@edit')->name('projects.edit')->middleware('message');
                Route::post('/atualizar/{id}',  'ProjectController@update')->name('projects.update');
                Route::get('/deletar/{id}',     'ProjectController@delete')->name('projects.delete')->middleware('message');

                Route::post('/blocos',          'ProjectController@buildings')->name('projects.buildings');
                Route::post('/quadras',         'ProjectController@blocks')->name('projects.blocks');

                Route::get('/imobliaria/contrato/enviar', 'ProjectController@companyContractSend')->name('projects.company.contract.send')->middleware('message');

                Route::post('/salvar/mapa',                 'ProjectController@map')->name('projects.map');
                Route::post('/salvar/proprietarios',        'ProjectController@owner')->name('projects.owner');
                Route::post('/salvar/financeiro',           'ProjectController@billing')->name('projects.billing');
                Route::post('/salvar/financeiro/metodo',    'ProjectController@billing_method')->name('projects.billing.method');
            });

            Route::group(['middleware' => ['is_admin_incorp']], function () {
                Route::post('/salvar/documento', 'ProjectController@document')->name('projects.document');
            });
        });

        Route::group(['prefix' => 'empreendimentos', 'middleware' => ['is_admin_incorp']], function () {
            Route::get('/deletar/documento/{id}', 'ProjectController@deleteDocument')->name('projects.document.delete')->middleware('message');
        });

        Route::group(['prefix' => 'empreendimentos'], function () {
            Route::get('/documento/{file}', 'ProjectController@download')->name('projects.download')->middleware('message');
        });

        Route::prefix('mapa-de-vendas')->group(function () {
            Route::match(['get', 'post'], '/', 'MapController@index')->name('map.index')->middleware('message');
            Route::get('/todos', 'MapController@all')->name('map.all')->middleware('message');
            Route::get('/exportar', 'MapController@export')->name('map.export')->middleware('message');
        });

        Route::prefix('propostas')->group(function () {
            Route::get('/', 'ProposalController@index')->name('proposals.index')->middleware('message');
            Route::get('/criar', 'ProposalController@create')->name('proposals.create')->middleware('message');
            Route::post('/salvar', 'ProposalController@store')->name('proposals.store');
            Route::get('/imprimir', 'ProposalController@print')->name('proposals.print')->middleware('message');
            Route::get('/visualizar', 'ProposalController@see')->name('proposals.show')->middleware('message');
            Route::get('/editar/{id}', 'ProposalController@edit')->name('proposals.edit')->middleware('message');
            Route::post('/atualizar/{id}', 'ProposalController@update')->name('proposals.update');
            Route::post('/alterar-status', 'ProposalController@status')->name('proposals.status')->middleware('message');
            Route::post('/salvar/documento', 'ProposalController@document')->name('proposals.document')->middleware('message');
            Route::get('/documento/{file}', 'ProposalController@download')->name('proposals.download')->middleware('message');
            Route::post('/buscar/documento', 'ProposalController@search')->name('proposals.search');
            Route::get('/deletar/documento/{id}', 'ProposalController@deleteDocument')->name('proposals.document.delete')->middleware('message');

            Route::prefix('carregar')->group(function () {
                Route::post('/status',          'ProposalController@loadStatus')->name('proposals.carregar.status');
                Route::post('/historico',       'ProposalController@loadHistory')->name('proposals.carregar.historico');
                Route::post('/documentacao',    'ProposalController@loadDocs')->name('proposals.carregar.documentacao');
                Route::post('/emails',          'ProposalController@loadEmails')->name('proposals.carregar.emails');
                Route::post('/whatsapp',        'ProposalController@loadWhatsApp')->name('proposals.carregar.whatsapp');
            });
        });

        Route::group(['prefix' => 'usuarios', 'middleware' => ['is_admin_incorp_coord']], function () {
            // Route::get('/', 'UserController@index')->name('users.index')->middleware('message');
            Route::match(['get', 'post'], '/', 'UserController@index')->name('users.index');
            Route::get('/criar', 'UserController@create')->name('users.create')->middleware('message');
            Route::get('/criar/sem-imobiliaria', 'UserController@create2')->name('users.create2')->middleware('message');
            Route::post('/salvar', 'UserController@store')->name('users.store');
            Route::post('/salvar/sem-imobiliaria', 'UserController@store2')->name('users.store2');
            Route::post('/vincular', 'UserController@attach')->name('users.attach');
            // Route::get('/detalhes/{id}', 'UserController@show')->name('users.show')->middleware('message');
            Route::get('/editar/{id}', 'UserController@edit')->name('users.edit')->middleware('message');
            Route::get('/dettach/{id}', 'UserController@dettach')->name('users.dettach')->middleware('message');
            Route::post('/atualizar/{id}', 'UserController@update')->name('users.update');
            Route::get('/deletar/{id}', 'UserController@delete')->name('users.delete')->middleware('message');
            Route::post('/salvar/usuario', 'UserController@user')->name('users.user');
            Route::get('/editar/sem-imobiliaria/{id}', 'UserController@edit_free')->name('users.edit.free')->middleware('message');
            Route::post('/atualizar/sem-imobiliaria/{id}', 'UserController@update_free')->name('users.update.free');
            //Route::get('/attach/admin', 'UserController@attach_admin')->name('users.attach_admin');
        });

        Route::prefix('mensagens')->group(function () {
            Route::get('/', 'MessageController@index')->name('messages.index')->middleware('message');
            Route::post('/enviar', 'MessageController@send')->name('messages.send');
            Route::match(['get', 'post'], '/ler', 'MessageController@read')->name('messages.read');
        });

        Route::prefix('imoveis')->group(function () {
            Route::post('/salvar', 'PropertyController@store')->name('properties.store');
            Route::post('/atualizar/{id}', 'PropertyController@update')->name('properties.update');
        });

        Route::group(['prefix' => 'contratos', 'middleware' => ['is_admin']], function () {
            Route::get('/',                 'ContractController@index')->name('contracts.index')->middleware('message');
            Route::get('/criar',            'ContractController@create')->name('contracts.create')->middleware('message');
            Route::post('/salvar',          'ContractController@store')->name('contracts.store');
            Route::get('/editar/{id}',      'ContractController@edit')->name('contracts.edit')->middleware('message');
            Route::post('/atualizar/{id}',  'ContractController@update')->name('contracts.update');
            Route::get('/gerar',            'ContractController@generate')->name('contracts.generate')->middleware('message');
            //Route::get('/documento/{file}', 'ContractController@download')->name('contracts.download')->middleware('message');
        });

        Route::prefix('contratos')->group(function () {
            Route::get('/documento/{file}', 'ContractController@download')->name('contracts.download')->middleware('message');
            Route::post('/enviar',          'ContractController@send')->name('contracts.send')->middleware('message');
        });

        Route::group(['prefix' => 'incorporadoras', 'middleware' => ['is_admin']], function () {
            Route::get('/', 'ConstructorController@index')->name('constructors.index')->middleware('message');
            Route::get('/criar', 'ConstructorController@create')->name('constructors.create')->middleware('message');
            Route::post('/salvar', 'ConstructorController@store')->name('constructors.store');
            // Route::get('/detalhes/{id}', 'ConstructorController@show')->name('constructors.show')->middleware('message');
            Route::get('/editar/{id}', 'ConstructorController@edit')->name('constructors.edit')->middleware('message');
            Route::post('/atualizar/{id}', 'ConstructorController@update')->name('constructors.update');
            // Route::get('/deletar/{id}', 'ConstructorController@delete')->name('constructors.delete')->middleware('message');
        });

        Route::group(['prefix' => 'relatorios', 'middleware' => ['is_admin_incorp']], function () {
            Route::get('/pagamentos',   'ReportController@payments')->name('reports.payments')->middleware('message');
            Route::get('/cobrancas',    'ReportController@billing')->name('reports.billing')->middleware('message');
        });

        Route::group(['prefix' => 'configuracoes', 'middleware' => ['is_admin']], function () {
            Route::match(['get', 'post'],   '/',     'SettingController@index')->name('settings');
        });

        Route::group(['prefix' => 'engenharia', 'middleware' => ['is_admin_incorp_engineer']], function () {
            Route::get('/',     'EngineerController@index')->name('engineer.index');
            Route::post('/',    'EngineerController@stage')->name('engineer.stage');
        });

        Route::group(['prefix' => 'financeiro', 'middleware' => ['is_admin_incorp']], function () {
            Route::get('/',                         'BillingController@index')->name('billing.index');
            Route::get('/detalhes/{id}',            'BillingController@details')->name('billing.details');
            Route::post('/gerar',                   'BillingController@generate')->name('billing.generate');
            Route::post('/cobranca/detalhes',       'BillingController@billing')->name('billing.billing');
            // Route::get('/boleto/visualizacao',      'BillingController@billet')                 ->name('billing.billet');
            Route::get('/boleto/baixa/consulta',    'BillingController@billet_baixa')->name('billing.billet.baixa');
            Route::get('/boleto/email/reenviar',    'BillingController@email_sent')->name('billing.billet.email.sent');
            Route::post('/baixa/reajuste',          'BillingController@paid_manual_value')->name('billing.paid.manual.value');
            Route::post('/baixa/calcular',          'BillingController@paid_manual_calculate')->name('billing.paid.manual.calculate');
            Route::post('/baixa/pagamento',         'BillingController@paid_manual_payment')->name('billing.paid.manual.payment');
            Route::get('/baixa/recibo',             'BillingController@paid_manual_receipt')->name('billing.paid.manual.receipt');
            Route::post('/antecipacao/valores',     'BillingController@ahead_value')->name('billing.ahead.value');
            Route::post('/antecipacao/pagamento',   'BillingController@ahead_payment')->name('billing.ahead.payment');
            Route::post('/antecipacao/total',       'BillingController@ahead_total_value')->name('billing.ahead.total.value');
            Route::post('/antecipacao/total/gerar', 'BillingController@ahead_total_generate')->name('billing.ahead.total.generate');
            // Route::get('/boleto/remessa',           'BillingController@billet_remessa')         ->name('billing.billet.remessa');
            Route::post('/boleto/remessa',          'BillingController@billet_remessa')->name('billing.billet.remessa');
            Route::post('/boleto/teste',            'BillingController@billet_test')->name('billing.billet.test');
            Route::post('/boleto/teste/gerar',      'BillingController@billet_test_generate')->name('billing.billet.test.generate');
            Route::post('/boleto/teste/ver',        'BillingController@billet_test_view')->name('billing.billet.test.view');
            Route::post('/boleto/send/whatsapp',    'BillingController@billet_send_whatsapp')->name('billing.billet.send.whatsapp');
        });

        Route::group(['prefix' => 'proprietarios', 'middleware' => ['is_admin']], function () {
            Route::get('/',                 'OwnerController@index')->name('owners.index')->middleware('message');
            Route::get('/criar',            'OwnerController@create')->name('owners.create')->middleware('message');
            Route::post('/salvar',          'OwnerController@store')->name('owners.store');
            Route::get('/visualizar/{id}',  'OwnerController@show')->name('owners.show')->middleware('message');
            Route::get('/editar/{id}',      'OwnerController@edit')->name('owners.edit')->middleware('message');
            Route::post('/atualizar/{id}',  'OwnerController@update')->name('owners.update');
            Route::get('/deletar/{id}',     'OwnerController@delete')->name('owners.delete')->middleware('message');
            Route::post('/busca',           'OwnerController@search')->name('owners.search');
        });

        Route::prefix('ricardo')->group(function () {
            Route::get('/tests', 'TestController@index')->name('tests.index');
        });
    });
});

Route::fallback('IndexController@error');

// Route::group(['domain' => 'mg2incorp.com.br'], $appRoutes);
// Route::group(['domain' => 'www.mg2incorp.com.br'], $appRoutes);
//
// Route::domain('cliente.mg2incorp.com.br')->group(function () {});