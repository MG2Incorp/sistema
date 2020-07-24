<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            h6 { font-weight: lighter !important; padding: 0 !important; margin: 0 !important }
            b { font-weight: bolder !important }
        </style>
    </head>
    <body>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td><h6>AUTORIZAÇÃO DE VENDA DE IMÓVEL</h6></td>
                    <td style="text-align: right"><h6><i>RESOLUÇÃO COFECI Nº 005/78</i></h6></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <h3 style="text-align: center"><u><b>AUTORIZAÇÃO DE VENDA DE IMÓVEL SEM EXCLUSIVIDADE</b></u></h3>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6><b>DAS PARTES</b></h6>
                        <h6 style="text-align: justify">{{ @$user_project->project->social_name }}, inscrito no CNPJ sob nº {{ @$user_project->project->cnpj }}, adiante denominada apenas <b>VENDEDOR(A)</b> autoriza a intermediação pela {{ @$user_project->company->name }}, <i>inscrita no CNPJ sob nº {{ @$user_project->company->cnpj }} e CRECI {{ @$user_project->company->creci }}</i>, adiante denominada apenas <b>INTERMEDIADOR(A)</b> para promover a venda da incorporação imobiliária de sua propriedade denominada, {{ @$user_project->project->name }}, com incorporação devidamente {{ @$user_project->project->notes }}, <i>esta autorização é extensiva a(o) Corretor(a) de Imóveis {{ @$user->name }}, CRECI Nº {{ @$user->creci }}, qual declara estar vinculado a <b>INTERMEDIADORA</b> e devidamente regularizado(a) junto ao CRECI, COFECI, SINDIMOVEIS e quaisquer outras entidades correlatas.</i></h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6><b>DO REGULAMENTO INTERNO</b></h6>
                        <h6 style="text-align: justify">Todo o corpo de clausulas a seguir do presente instrumento particular, objetiva a implementação e o estabelecimento de uma convenção particular entre as partes contratantes, convenção que é reconhecida pela(o) <b>INTERMEDIADOR(A)</b> como código cogente na relação que manterá com o(a) <b>VENDEDOR(A)</b> tendo suas disposições força legal capaz de resolver e dirimir qualquer duvida que possa surgir entre as partes. Eventuais casos omissos que venha a surgir, serão regulados pela Lei Federal nº 6.530/78, Decreto 81.871/78, Resolução do COFECI, artigos 722 a 729 do Código Civil, e demais legislações pertinentes em vigor aplicável à espécie.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6><b>DECLARAÇÃO DA INTERMEDIADORA</b></h6>
                        <h6 style="text-align: justify">O(a) INTERMEDIADOR(A) assume incontinentemente toda e qualquer responsabilidade por todos os encargos sociais, previdenciários, rescisórios, trabalhistas e fiscais de todos seus prepostos e mentos de sua equipe de vendas, bem como por todos os danos culposos ou dolosos, que os mesmos vierem a ocasionar, ainda que em bens ou pessoas estranhas ao presente contrato.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6><b>PROCEDIMENTOS DE VENDAS</b></h6>
                        <h6 style="text-align: justify">1ª – O valor de venda, formas de pagamentos e espelho de vendas deverá ser observado no sistema <b>MG2 – INCORP</b>, qual deve ser acessado pelo endereço www.mg2incorp.com.br com seu usuário e senha. Da mesma forma, as reservas só serão aceitas pelo sistema, qual se expiram automaticamente em {{ @$user_project->project->expiration_time }} horas, caso não seja incluídos documentos necessários para analise e elaboração contratual.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: justify">2ª - Pelos serviços prestados ora pactuados o(a) <b>VENDEDOR(A)</b> pagará à <b>INTERMEDIADORA</b>, desde que ocorra regular e efetiva compensação do pagamento a titulo de “Sinal” e “Principio de Pagamento” a título de comissão de corretagem imobiliária a porcentagem estipulada em contrato entre as partes, qual a <b>INTERMEDIADORA</b> declara desde já que fica responsável pelo repasse ao <b>CORRETOR</b> responsável.</h6>
                        <h6 style="text-align: justify; text-indent: 4em">(S/N) Em caso de propostas que o(a) <b>VENDEDOR(A)</b> eventualmente vier a aprovar expressamente parcelamento do pagamento do sinal e principio de pagamento, fica desde já esclarecido que a mesma regra e periocidade será aplicadas na remuneração da <b>INTERMEDIADOR(A)</b>.</h6>
                        <h6 style="text-align: justify; text-indent: 4em">A comissão será paga sobre o valor da transação, desde que a imobiliária apresente proposta e contratos devidamente assinados e reconhecido firma pelo(s) <b>INTERMEDIADOR(A, ES)</b> juntamente com a assinatura do(a) <b>CORRETOR(A)</b>, e que venha a ser aceita pelo(a) <b>VENDEDOR(A)</b> e a venda concretizada, independente das condições aqui acordadas, convencionada será devida conforme artigos 725 e 727 do Código Civil Brasileiro em vigor.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: justify">3ª – A presente Autorização vigorará por prazo indeterminado a contar de sua assinatura, sendo revogado automaticamente com a venda total do empreendimento ou por meio de descredenciamento da imobiliária ou corretor por motivos alheios a este. Não sendo necessário realizar manifestação por escrito as partes.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: justify">4ª – A presente autorização proíbe o recebimento de qualquer valor a titulo de sinal, devendo ser pago diretamente através de deposito bancário e ou boleto bancário na conta da <b>VENDEDORA</b>.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: justify">5ª- O(a) <b>VENDEDOR(A)</b> declara ser verdadeiro todos os dados da incorporação informado acima.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: justify">E por estarem assim acordados, assinam a presente autorização.</h6>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6 style="text-align: right">{{ $user_project->project->local }}, <?php setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese'); echo utf8_encode(strftime('%d de %B de %Y', strtotime('today'))); ?></h6>
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td><h6 style="text-align: left"><b>{{ @$user_project->company->name }}</b></h6></td>
                    <td><h6 style="text-align: right"><b>{{ @$user_project->project->social_name }}</b></h6></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <h6 style="text-align: justify">Este documento foi gerado por <b>MG2 – INCORP</b> e <b>ASSINADO DIGITALMENTE</b> por {{ @$user_project->project->constructor->name }}, sob nº: {{ @$user_project->code }} expedido em {{ formatData(date('Y-m-d')) }}. A aceitação desta autorização condiciona-se à verificação de sua autenticidade no portal do MG2 – Incorp (http://www.mg2incorp.com.br/validacao).</h6>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>