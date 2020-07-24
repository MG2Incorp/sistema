<?php

namespace App\Api;

class PlugBoleto {

    // private $url = "http://homologacao.plugboleto.com.br/api/v1";
    private $url = "https://plugboleto.com.br/api/v1";
    //private $url = "http://webhook.site/d4aa6533-4474-4e14-9255-5e4cc1791977";

    // private $cnpj_sh = '01001001000113';
    // private $token_sh = 'f22b97c0c9a3d41ac0a3875aba69e5aa';
    private $cnpj_sh = '31401226833';
    private $token_sh = '7acc18dcb12d01db7f880e9ee5123688';

    private $cnpj_cedente = '01001001000113';
    private $token_cedente = 'ff5bf3acfe4eb3e70b0e94ea21d01b3c';

    /* CEDENTE */
    public function getCedente($doc) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];
        return $this->curl($this->url.'/cedentes?cpf_cnpj='.onlyNumber($doc), 'get', $headers);
    }

    public function getCedentes() {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];
        return $this->curl($this->url.'/cedentes', 'get', $headers);
    }

    public function postCedente($owner) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];

        $array = [
            "CedenteRazaoSocial"            => $owner->social_name,
            "CedenteNomeFantasia"           => $owner->name,
            "CedenteCPFCNPJ"                => onlyNumber($owner->document),
            "CedenteEnderecoLogradouro"     => $owner->logradouro,
            "CedenteEnderecoNumero"         => $owner->numero,
            "CedenteEnderecoComplemento"    => $owner->complemento,
            "CedenteEnderecoBairro"         => $owner->bairro,
            "CedenteEnderecoCEP"            => onlyNumber($owner->cep),
            "CedenteEnderecoCidadeIBGE"     => onlyNumber($owner->cidade_ibge),
            "CedenteTelefone"               => $owner->telefone,
            "CedenteEmail"                  => $owner->email
        ];

        return $this->curl($this->url.'/cedentes', 'post', $headers, $array);
    }

    public function putCedente($owner) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($owner->document) ];

        $array = [
            "CedenteRazaoSocial"            => $owner->social_name,
            "CedenteNomeFantasia"           => $owner->name,
            "CedenteCPFCNPJ"                => onlyNumber($owner->document),
            "CedenteEnderecoLogradouro"     => $owner->logradouro,
            "CedenteEnderecoNumero"         => $owner->numero,
            "CedenteEnderecoComplemento"    => $owner->complemento,
            "CedenteEnderecoBairro"         => $owner->bairro,
            "CedenteEnderecoCEP"            => onlyNumber($owner->cep),
            "CedenteEnderecoCidadeIBGE"     => onlyNumber($owner->cidade_ibge),
            "CedenteTelefone"               => $owner->telefone,
            "CedenteEmail"                  => $owner->email
        ];

        // \Log::info('CEDENTE ENVIO: '.serialize($array));

        return $this->curl($this->url.'/cedentes/'.$owner->plugboleto_id, 'put', $headers, $array);
    }

    /* CONTA */
    public function getConta($account) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($account->owner->document) ];

        $array = [];
        if($account->bank_code) $array[] = 'codigo_banco='.$account->bank_code;
        if($account->agency) $array[] = 'agencia='.$account->agency;
        if($account->agency_dv) $array[] = 'agencia_dv='.$account->agency_dv;
        if($account->number) $array[] = 'conta='.$account->number;
        if($account->number_dv) $array[] = 'conta_dv='.$account->number_dv;
        if($account->plugboleto_id) $array[] = 'id='.$account->plugboleto_id;

        \Log::info("GET CONTA IMPLODE: ".implode('&', $array));

        if(count($array)) return $this->curl($this->url.'/cedentes/contas?'.implode('&', $array), 'get', $headers);
        return null;
    }

    public function getContas() {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];
        return $this->curl($this->url.'/cedentes/contas', 'get', $headers);
    }

    public function postConta($account) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($account->owner->document) ];

        $array = [
            "ContaCodigoBanco"          => $account->bank_code,
            "ContaAgencia"              => $account->agency,
            "ContaAgenciaDV"            => $account->agency_dv,
            "ContaNumero"               => $account->number,
            "ContaNumeroDV"             => $account->number_dv,
            "ContaTipo"                 => $account->type,
            "ContaCodigoBeneficiario"   => $account->beneficiario,
            "ContaCodigoEmpresa"        => $account->company_code,
            "ContaValidacaoAtiva"       => true,
            "ContaImpressaoAtualizada"  => true
        ];

        // \Log::info($array);

        $retorno = $this->curl($this->url.'/cedentes/contas', 'post', $headers, $array);

        // \Log::info(serialize($retorno));

        return $retorno;
    }

    public function putConta($account) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($account->owner->document) ];

        $array = [
            "ContaCodigoBanco"          => $account->bank_code,
            "ContaAgencia"              => $account->agency,
            "ContaAgenciaDV"            => $account->agency_dv,
            "ContaNumero"               => $account->number,
            "ContaNumeroDV"             => $account->number_dv,
            "ContaTipo"                 => $account->type,
            "ContaCodigoBeneficiario"   => $account->beneficiario,
            "ContaCodigoEmpresa"        => $account->company_code,
            "ContaValidacaoAtiva"       => true,
            "ContaImpressaoAtualizada"  => true
        ];

        // \Log::info("PUT CONTA ENVIO: ".serialize($array));

        return $this->curl($this->url.'/cedentes/contas/'.$account->plugboleto_id, 'put', $headers, $array);
    }

    /* CONVENIO */
    public function getConvenio($agreement) {
        // $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($agreement->account->owner->document) ];

        $array = [];
        if($agreement->numero) $array[] = 'numero_convenio='.$agreement->numero;
        if($agreement->carteira) $array[] = 'carteira='.$agreement->carteira;
        // if($agreement->cnab) $array[] = 'padraoCNAB='.$agreement->cnab;
        if($agreement->plugboleto_id) $array[] = 'id='.$agreement->plugboleto_id;

        // \Log::info("GET CONVENIO IMPLODE: ".implode('&', $array));

        if(count($array)) return $this->curl($this->url.'/cedentes/contas/convenios?'.implode('&', $array), 'get', $headers);
        return null;
    }

    public function getConvenios() {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh ];
        return $this->curl($this->url.'/cedentes/contas/convenios', 'get', $headers);
    }

    public function postConvenio($agreement) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($agreement->account->owner->document) ];

        $array = [
            "ConvenioNumero"                => $agreement->numero,
            "ConvenioDescricao"             => $agreement->descricao,
            "ConvenioCarteira"              => $agreement->carteira,
            "ConvenioEspecie"               => 'R$',
            "ConvenioPadraoCNAB"            => $agreement->cnab,
            "ConvenioReiniciarDiariamente"  => $agreement->reiniciar ? true : false,
            "ConvenioNumeroRemessa"         => !$agreement->reiniciar ? $agreement->numero_remessa : null,
            "Conta"                         => $agreement->account->plugboleto_id,
            "ConvenioUtilizaVan"            => $agreement->utiliza_van,
            "ConvenioDensidadeRemessa"      => $agreement->densidade_remessa,
            "ConvenioNossoNumeroBanco"      => $agreement->nosso_numero_banco ? true : false
        ];

        return $this->curl($this->url.'/cedentes/contas/convenios', 'post', $headers, $array);
    }

    public function putConvenio($agreement) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber($agreement->account->owner->document) ];

        $array = [
            "ConvenioNumero"                => $agreement->numero,
            "ConvenioDescricao"             => $agreement->descricao,
            "ConvenioCarteira"              => $agreement->carteira,
            "ConvenioEspecie"               => 'R$',
            "ConvenioPadraoCNAB"            => $agreement->cnab,
            "ConvenioReiniciarDiariamente"  => $agreement->reiniciar ? true : false,
            "ConvenioNumeroRemessa"         => !$agreement->reiniciar ? $agreement->numero_remessa : null,
            "Conta"                         => $agreement->account->plugboleto_id,
            "ConvenioUtilizaVan"            => $agreement->utiliza_van,
            "ConvenioDensidadeRemessa"      => $agreement->densidade_remessa,
            "ConvenioNossoNumeroBanco"      => $agreement->nosso_numero_banco ? true : false
        ];

        return $this->curl($this->url.'/cedentes/contas/convenios/'.$agreement->plugboleto_id, 'put', $headers, $array);
    }

    /* BOLETO */
    public function postBoleto($billing, $billet) {

        $payment = $billing->payment;

        $count_parcelas = $payment->billings->count();

        $index = $payment->billings->pluck('id')->search($billing->id) + 1;

        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];

        if(!$project_owner = \App\ProjectOwner::where('account_id', $payment->proposal->property->account_id)->where('project_id', $payment->proposal->property->block->building->project_id)->first()) return "IMPOSSIBLE";

        $array = [];

        $vencimento = \Carbon\Carbon::parse($billing->expires_at);
        $vencimento_string = $vencimento->toDateString();

        $instrucoes = "";

        switch ($project_owner->TituloCodigoMulta) {
            case 0: $instrucoes .= ""; break;
            case 1: $instrucoes .= " multa de R$ ".formatMoney($project_owner->TituloValorMultaTaxa);   break;
            case 2: $instrucoes .= " multa de ".formatMoney($project_owner->TituloValorMultaTaxa)." %"; break;
            default: break;
        }

        if($project_owner->TituloCodigoMulta != 0 && $project_owner->TituloCodigoJuros != 3) $instrucoes .= " +";

        switch ($project_owner->TituloCodigoJuros) {
            case 1: $instrucoes .= " juros de R$ ".formatMoney($project_owner->TituloValorJuros)." ao dia"; break;
            case 2: $instrucoes .= " juros de ".formatMoney($project_owner->TituloValorJuros)." % ao mês"; break;
            case 3: $instrucoes .= ""; break;
            default: break;
        }

        $array[0] = [
            "SacadoCPFCNPJ"                 => onlyNumber($payment->proposal->main_proponent->document),
            "SacadoEmail"                   => $payment->proposal->main_proponent->email,
            "SacadoEnderecoLogradouro"      => $payment->proposal->main_proponent->address->street,
            "SacadoEnderecoNumero"          => $payment->proposal->main_proponent->address->number,
            "SacadoEnderecoBairro"          => $payment->proposal->main_proponent->address->district,
            "SacadoEnderecoCep"             => onlyNumber($payment->proposal->main_proponent->address->zipcode),
            "SacadoEnderecoCidade"          => $payment->proposal->main_proponent->address->city,
            "SacadoEnderecoComplemento"     => $payment->proposal->main_proponent->address->complement,
            "SacadoEnderecoPais"            => "Brasil",
            "SacadoEnderecoUf"              => $payment->proposal->main_proponent->address->state,
            "SacadoNome"                    => $payment->proposal->main_proponent->name,
            "SacadoTelefone"                => onlyNumber($payment->proposal->main_proponent->phone),
            "SacadoCelular"                 => onlyNumber($payment->proposal->main_proponent->cellphone),

            "CedenteContaCodigoBanco"       => (string) $payment->proposal->property->account->bank_code,
            "CedenteContaNumero"            => $payment->proposal->property->account->number,
            "CedenteContaNumeroDV"          => $payment->proposal->property->account->number_dv,
            "CedenteConvenioNumero"         => $payment->proposal->property->account->agreement->numero,

            "TituloNossoNumero"             => $billet->id + $payment->proposal->property->account->inicio_nosso_numero,
            "TituloValor"                   => formatMoney($billing->value),
            "TituloNumeroDocumento"         => 'B-'.$billet->id,
            "TituloDataEmissao"             => formatData(\Carbon\Carbon::now()->toDateString()),
            // "TituloDataEmissao"             => formatData($vencimento_string),
            "TituloDataVencimento"          => formatData($vencimento_string),
            "TituloAceite"                  => $project_owner->TituloAceite,
            "TituloDocEspecie"              => $project_owner->TituloDocEspecie,
            "TituloLocalPagamento"          => $project_owner->TituloLocalPagamento,

            "TituloCodDesconto"             => $project_owner->TituloCodDesconto,
            "TituloDataDesconto"            => $project_owner->TituloCodDesconto ? formatData($vencimento->copy()->subDays($project_owner->TituloDataDesconto)->toDateString()) : null,
            "TituloValorDescontoTaxa"       => $project_owner->TituloCodDesconto ? formatMoney($project_owner->TituloValorDescontoTaxa) : null,

            "TituloCodigoJuros"             => $project_owner->TituloCodigoJuros,
            "TituloDataJuros"               => $project_owner->TituloCodigoJuros ? formatData($vencimento->copy()->addDays($project_owner->TituloDataJuros)->toDateString()) : null,
            "TituloValorJuros"              => $project_owner->TituloCodigoJuros ? formatMoney($project_owner->TituloValorJuros) : null,

            "TituloCodigoMulta"             => $project_owner->TituloCodigoMulta,
            "TituloDataMulta"               => $project_owner->TituloCodigoMulta ? formatData($vencimento->copy()->addDays($project_owner->TituloDataMulta)->toDateString()) : null,
            "TituloValorMultaTaxa"          => $project_owner->TituloCodigoMulta ? formatMoney($project_owner->TituloValorMultaTaxa) : null,

            "TituloCodProtesto"             => $project_owner->TituloCodProtesto,
            "TituloPrazoProtesto"           => $project_owner->TituloPrazoProtesto,

            "TituloCodBaixaDevolucao"       => $project_owner->TituloCodBaixaDevolucao,
            "TituloPrazoBaixa"              => $project_owner->TituloPrazoBaixa,

            "TituloMensagem01"              => "** NÃO SERÃO ACEITOS DEPÓSITOS NA CONTA CORRENTE DO CEDENTE **",
            "TituloMensagem02"              => str_limit($payment->proposal->property->block->building->project->name, 80),
            "TituloMensagem03"              => "Contrato: ".$payment->proposal->id,
            "TituloMensagem04"              => "Bloco: ".$payment->proposal->property->block->building->name,
            "TituloMensagem05"              => "Quadra/Andar: ".$payment->proposal->property->block->label,
            "TituloMensagem06"              => "Unidade: ".$payment->proposal->property->number,

            "TituloEmissaoBoleto"           => "B",
            "TituloCategoria"               => 2,
            "TituloPostagemBoleto"          => "N",
            "TituloCodEmissaoBloqueto"      => $project_owner->TituloCodEmissaoBloqueto,
            "TituloOutrosAcrescimos"        => null,

            "TituloInstrucoes"              => "Após o vencimento cobrar".$instrucoes,

            /* "TituloParcela"                 => str_pad($index, strlen($count_parcelas), '0', STR_PAD_LEFT)."/".$count_parcelas,*/
            "TituloVariacaoCarteira"        => null
        ];

        // \Log::info('TRY GENERATE BOLETO BILLING: '.serialize($array));

        //printa($array);

        return $this->curl($this->url.'/boletos/lote', 'post', $headers, $array);
    }

    public function postAheadBoleto($ahead, $billet) {

        $payment = $ahead->payment;

        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];

        if(!$project_owner = \App\ProjectOwner::where('account_id', $payment->proposal->property->account_id)->where('project_id', $payment->proposal->property->block->building->project_id)->first()) return "IMPOSSIBLE";

        $array = [];

        $vencimento = \Carbon\Carbon::now();
        $vencimento_string = $vencimento->toDateString();

        $array[0] = [
            "SacadoCPFCNPJ"                 => onlyNumber($payment->proposal->main_proponent->document),
            "SacadoEmail"                   => $payment->proposal->main_proponent->email,
            "SacadoEnderecoLogradouro"      => $payment->proposal->main_proponent->address->street,
            "SacadoEnderecoNumero"          => $payment->proposal->main_proponent->address->number,
            "SacadoEnderecoBairro"          => $payment->proposal->main_proponent->address->district,
            "SacadoEnderecoCep"             => onlyNumber($payment->proposal->main_proponent->address->zipcode),
            "SacadoEnderecoCidade"          => $payment->proposal->main_proponent->address->city,
            "SacadoEnderecoComplemento"     => $payment->proposal->main_proponent->address->complement,
            "SacadoEnderecoPais"            => "Brasil",
            "SacadoEnderecoUf"              => $payment->proposal->main_proponent->address->state,
            "SacadoNome"                    => $payment->proposal->main_proponent->name,
            "SacadoTelefone"                => onlyNumber($payment->proposal->main_proponent->phone),
            "SacadoCelular"                 => onlyNumber($payment->proposal->main_proponent->cellphone),

            "CedenteContaCodigoBanco"       => (string) $payment->proposal->property->account->bank_code,
            "CedenteContaNumero"            => $payment->proposal->property->account->number,
            "CedenteContaNumeroDV"          => $payment->proposal->property->account->number_dv,
            "CedenteConvenioNumero"         => $payment->proposal->property->account->agreement->numero,

            "TituloNossoNumero"             => $billet->id + $payment->proposal->property->account->inicio_nosso_numero,
            "TituloValor"                   => formatMoney($ahead->value),
            "TituloNumeroDocumento"         => 'A-'.$ahead->id,
            "TituloDataEmissao"             => formatData(\Carbon\Carbon::now()->toDateString()),
            "TituloDataVencimento"          => formatData($vencimento_string),
            "TituloAceite"                  => $project_owner->TituloAceite,
            "TituloDocEspecie"              => $project_owner->TituloDocEspecie,
            "TituloLocalPagamento"          => $project_owner->TituloLocalPagamento,

            "TituloCodBaixaDevolucao"       => $project_owner->TituloCodBaixaDevolucao,
            "TituloPrazoBaixa"              => $project_owner->TituloPrazoBaixa,

            "TituloMensagem01"              => "** NÃO SERÃO ACEITOS DEPÓSITOS NA CONTA CORRENTE DO CEDENTE **",
            "TituloMensagem02"              => str_limit($payment->proposal->property->block->building->project->name, 80),
            "TituloMensagem03"              => "Contrato: ".$payment->proposal->id,
            "TituloMensagem04"              => "Bloco: ".$payment->proposal->property->block->building->name,
            "TituloMensagem05"              => "Quadra/Andar: ".$payment->proposal->property->block->label,
            "TituloMensagem06"              => "Unidade: ".$payment->proposal->property->id,

            "TituloEmissaoBoleto"           => "B",
            "TituloCategoria"               => 2,
            "TituloPostagemBoleto"          => "N",
            "TituloCodEmissaoBloqueto"      => $project_owner->TituloCodEmissaoBloqueto,
            "TituloOutrosAcrescimos"        => null,

            "TituloInstrucoes"              => "Não receber após o vencimento.",
        ];

        //printa($array);

        return $this->curl($this->url.'/boletos/lote', 'post', $headers, $array);
    }

    public function postBoletoTest($account, $test) {
        $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];

        $array = [];

        for ($i = 0; $i < $test->quantity; $i++) {
            $billet = \App\Billet::create([
                'billetable_type'   => 'App\BillingTest',
                'billetable_id'     => $test->id,
                'token'             => getBillingToken(8)
            ]);

            $array[$i] = [
                "SacadoCPFCNPJ"                 => '31401226833',
                "SacadoEmail"                   => 'test@test.com.br',
                "SacadoEnderecoLogradouro"      => 'Avenida 13',
                "SacadoEnderecoNumero"          => '213',
                "SacadoEnderecoBairro"          => 'Saúde',
                "SacadoEnderecoCep"             => '13500340',
                "SacadoEnderecoCidade"          => 'Rio Claro',
                "SacadoEnderecoComplemento"     => '',
                "SacadoEnderecoPais"            => "Brasil",
                "SacadoEnderecoUf"              => 'SP',
                "SacadoNome"                    => 'Gustavo Marotti',
                "SacadoTelefone"                => '99999999',
                "SacadoCelular"                 => '998737550',

                "CedenteContaCodigoBanco"       => (string) $account->bank_code,
                "CedenteContaNumero"            => $account->number,
                "CedenteContaNumeroDV"          => $account->number_dv,
                "CedenteConvenioNumero"         => $account->agreement->numero,

                "TituloNossoNumero"             => $billet->id + $account->inicio_nosso_numero,
                "TituloValor"                   => formatMoney($test->value),
                "TituloNumeroDocumento"         => 'T-'.$billet->id,
                "TituloDataEmissao"             => formatData(\Carbon\Carbon::now()->toDateString()),
                "TituloDataVencimento"          => formatData(\Carbon\Carbon::now()->toDateString()),
                "TituloAceite"                  => $test->TituloAceite,
                "TituloDocEspecie"              => $test->TituloDocEspecie,
                "TituloLocalPagamento"          => $test->TituloLocalPagamento,

                "TituloMensagem01"              => "** BOLETO PARA TESTE DE HOMOLOGAÇÃO **",

                "TituloEmissaoBoleto"           => "B",
                "TituloCategoria"               => 2,
                "TituloPostagemBoleto"          => "N",
                "TituloCodEmissaoBloqueto"      => $test->TituloCodEmissaoBloqueto,
                "TituloOutrosAcrescimos"        => null,

                // "TituloInstrucoes"              => "Após o vencimento, cobrar multa de 2% e juros de 1% ao mês.",
                "TituloInstrucoes"              => "",
            ];
        }

        // printa($array);

        $retorno = $this->curl($this->url.'/boletos/lote', 'post', $headers, $array);

        \Log::info('RETORNO BOLETO TESTE: '.serialize($retorno));

        return $retorno;
    }

    public function getBoletos($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        return $this->curl($this->url.'/boletos?idintegracao='.$billet->idIntegracao, 'get', $headers);
    }

    public function getBoletosColecao($billets) {
        switch ($billets->first()->billetable_type) {
            case 'App\Billing':
                $payment = $billets->first()->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billets->first()->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billets->first()->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        $array = $billets->pluck('idIntegracao')->toArray();

        return $this->curl($this->url.'/boletos?idintegracao='.implode(',', $array), 'get', $headers);
    }

    public function solicitarPDF($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        $array = [ "TipoImpressao" => "0", "Boletos" => [ $billet->idIntegracao ] ];

        return $this->curl($this->url.'/boletos/impressao/lote', 'post', $headers, $array);
    }

    public function imprimirPDF($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        return $this->curl($this->url.'/boletos/impressao/lote/'.$billet->impressao, 'pdf', $headers);
    }

    public function gerarRemessa($billets) {
        switch ($billets->first()->billetable_type) {
            case 'App\Billing':
                $payment = $billets->first()->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billets->first()->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billets->first()->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        // $array = [ $billet->idIntegracao ];
        $array = $billets->pluck('idIntegracao')->toArray();

        return $this->curl($this->url.'/remessas/lote', 'post', $headers, $array);
    }

    public function gerarRemessaUnica($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        $array = [ $billet->idIntegracao ];

        return $this->curl($this->url.'/remessas/lote', 'post', $headers, $array);
    }

    public function solicitarBaixa($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        $array = [ $billet->idIntegracao ];

        return $this->curl($this->url.'/boletos/baixa/lote', 'post', $headers, $array);
    }

    public function imprimirPDFBaixa($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        return $this->curl($this->url.'/boletos/baixa/lote/'.$billet->id_baixa, 'get', $headers);
    }

    public function solicitarEmail($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        $num_parcela = $payment->billings->pluck('id')->search($billet->billetable->id) + 1;

        $message = '
            <div><img src="'.asset(env('PROJECTS_IMAGES_DIR').$payment->proposal->property->block->building->project->photo).'"></div>
            <br>
            <div>Prezado '.$payment->proposal->main_proponent->name.',<div>
            <br>
            <div>Você está recebendo o boleto referente a:</div>
            <br>
            <div><b>Contrato:</b> '.$payment->proposal_id.'</div>
            <div><b>Parcela:</b> '.$num_parcela.'/'.$payment->quantity.'</div>
            <div><b>Vencimento:</b> '.formatData($billet->billetable->expires_at).'</div>
            <div><b>Empreendimento:</b> '.$payment->proposal->property->block->building->project->name.'</div>
            <br>
            <a href="'.route('billing.billet').'?billet='.$billet->token.'">Clique aqui para visualizar o boleto</a>
            <br>
            <br>
            <div>Realize o pagamento até a data do vencimento evitando assim multas, juros e medidas cabíveis ao seu contrato.</div>
            <br>
            <div>'.$payment->proposal->property->block->building->project->name.'</div>
        ';

        $array = [
            "IdIntegracao"          => [ $billet->idIntegracao ],
            "EmailNomeRemetente"    => $payment->proposal->property->block->building->project->name,                            /* OBRIGATORIO */
            "EmailRemetente"        => "nao-responda@mg2incorp.com.br",                                                         /* OBRIGATORIO */
            "EmailAssunto"          => "Boleto para pagamento - ".$payment->proposal->property->block->building->project->name, /* OBRIGATORIO */
            "EmailMensagem"         => $message,                                                                                /* OBRIGATORIO */
            "EmailDestinatario"     => [ $payment->proposal->main_proponent->email ],                                           /* OBRIGATORIO */
            "EmailAnexarBoleto"     => true,
            "EmailConteudoHtml"     => true,
            "TipoImpressao"         => "0",
            // "EmailCco"              => [ 'suporte@inovecommerce.com.br' ],
            "EmailCco"              => [],
            "EmailResponderPara"    => ""
        ];

        return $this->curl($this->url.'/email/lote', 'post', $headers, $array);
    }

    public function consultarEnvioEmail($billet) {
        switch ($billet->billetable_type) {
            case 'App\Billing':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\Ahead':
                $payment = $billet->billetable->payment;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$payment->proposal->property->account->owner->document) ];
            break;
            case 'App\BillingTest':
                $account = $billet->billetable->account;
                $headers = [ 'Content-Type: application/json', 'cnpj-sh: '.$this->cnpj_sh, 'token-sh: '.$this->token_sh, 'cnpj-cedente: '.onlyNumber(@$account->owner->document) ];
            break;

            default: break;
        }

        return $this->curl($this->url.'/email/lote/'.$billet->email_sent_protocol, 'get', $headers);
    }

    public function curl($url, $action, $headers, $post = array()) {
        switch ($action) {
            case 'pdf':  $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => false ); break;
            case 'get':  $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => false ); break;
            case 'post': $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 60, CURLOPT_POST => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => false, CURLOPT_POSTFIELDS => json_encode($post) ); break;
            case 'put':  $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 60, CURLOPT_POST => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => false, CURLOPT_POSTFIELDS => json_encode($post), CURLOPT_CUSTOMREQUEST => 'PUT' ); break;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $opt);
        $xml = curl_exec($curl);
        curl_close($curl);

        $retorno = json_decode($xml);
        if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) \Log::info('ERRO PLUG BOLETO: '.$xml);

        if($action == 'pdf') return $xml;

        return json_decode($xml);
    }
}
