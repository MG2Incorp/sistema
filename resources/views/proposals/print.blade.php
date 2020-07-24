@extends('layouts.app')
@section('css')
    <style>
        * { border-radius: 0 !important }
        .hr { border-color: grey !important }
        .metade1 { width: 702px !important }
        .metade2 { width: 468px !important }
        .quarto { width: 25% !important }
        .terco { width: 390px !important }
        .yellow { background-color: #FFC107 !important }
        .grey { background-color: lightgrey !important }
        table *{ padding: 3px !important; }
        /* #proposal * { font-size: 12px !important } */
        html, body { font-size:12px !important }
        @media print {
            td.yellow {
                background-color: #FFC107 !important;
                -webkit-print-color-adjust: exact;
            }
            td.grey {
                background-color: lightgrey !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection
@section('content')
    <?php setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese'); ?>
    <div class="container" id="proposal">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row form-group" style="height: 150px">
                            <div class="col-3 d-flex justify-content-center">
                                <img src="{{ asset(env('PROJECTS_IMAGES_DIR').$proposal->property->block->building->project->photo2) }}" style="max-height: 140px" class="align-self-center">
                            </div>
                            <div class="col-6">
                                <h5 class="m-0 p-0 text-center">Proposta emitida por MG2 Incorp</h5>
                                <h5 class="mt-0 text-center"><small>www.mg2incorp.com.br (19) 3500.8414</small></h5>
                                <hr class="m-0">
                                <h4 class="mb-0 mt-2 text-center"><b>Proposta de Compra Imóvel:</b></h4>
                                <h4 class="mt-0 text-center"><b>{{ $proposal->property->block->building->project->name }}</b></h4>
                                <hr class="mt-0">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="text-center">Proposta Número: #{{ $proposal->id }}</h5>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-center">Data da Proposta: {{ dateString($proposal->created_at) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <img src="{{ asset('img/logo.png') }}" style="max-height: 140px" class="align-self-center">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <td class="grey" colspan="6"><b>Dados do Empreendimento, Bloco, Unidade, Proposta e Corretor</b></td>
                        </tr>
                        <tr>
                            @if($proposal->property->account_id)
                                <td class="metade1" colspan="3">Empreendimento: {{ $proposal->property->block->building->project->name }}</td>
                                <td class="metade2" colspan="3">Proprietário: {{ $proposal->property->account->owner->social_name }}</td>
                            @else
                                <td colspan="6">Empreendimento: {{ $proposal->property->block->building->project->name }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="metade1" colspan="3">Imobiliária: {{ @$proposal->user->user_projects->where('project_id', $proposal->property->block->building->project->id)->first()->company->name }}</td>
                            <td class="metade2" colspan="3">Bloco: {{ $proposal->property->block->building->name }}</td>
                        </tr>
                        <tr>
                            <td class="metade1" colspan="3">Corretor: {{ $proposal->user->name }}</td>
                            <td class="metade2" colspan="3">Quadra/Andar: {{ $proposal->property->block->label }} - Unidade: {{ $proposal->property->number }}</td>
                        </tr>
                        <tr>
                            <td class="metade1 p-0" colspan="3">
                                <table class="w-100 table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="border-right">Valor do Imóvel: R$ {{ formatMoney($proposal->property->value) }}</td>
                                            <td>Valor do Desconto: R$ {{ formatMoney($proposal->discount) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="metade2 p-0" colspan="3">
                                <table class="w-100 table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="border-right">Valor Final: R$ {{ formatMoney($proposal->property->value - $proposal->discount) }}</td>
                                            <td>Valor da Proposta: R$ {{ formatMoney($proposal->payments->sum('total_value')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">Dimensões: {{ $proposal->property->dimensions }}</td>
                        </tr>
                        <tr>
                            <td colspan="6">Observações do Imóvel: {{ $proposal->property->notes }}</td>
                        </tr>
                        <tr>
                            <td class="terco" colspan="2">Mídia: {{ $proposal->media }}</td>
                            <td class="terco" colspan="2">Área: {{ $proposal->property->size }} m²</td>
                            <td class="terco" colspan="2">Motivo da Compra: {{ $proposal->reason }}</td>
                        </tr>
                        @foreach($proposal->proponents as $key => $proponent)
                            <tr>
                                <td class="grey" colspan="6"><b>Dados do Proponente {{ $key+1 }}</b></td>
                            </tr>
                            <tr>
                                <td class="metade1" colspan="3">Nome: {{ $proponent->name }}</td>
                                <td class="metade2" colspan="3">Sexo: {{ $proponent->gender }}</td>
                            </tr>
                            <tr>
                                <td class="terco" colspan="2">Proporção: {{ $proponent->proportion }} %</td>
                                <td class="terco" colspan="2">Data de Nascimento: {{ formatData($proponent->birthdate) }}</td>
                                <td class="terco" colspan="2">Estado Civil: {{ $proponent->civic_status }}</td>
                            </tr>
                            <tr>
                                <td class="terco" colspan="2">CPF/CNPJ: {{ $proponent->document }}</td>
                                <td class="terco" colspan="2">RG: {{ $proponent->rg }}</td>
                                <td class="terco" colspan="2">RG Orgão/UF: {{ $proponent->emitter }}/{{ $proponent->rg_state }}</td>
                            </tr>
                            <tr>
                                <td class="metade1" colspan="3">Nacionalidade: {{ $proponent->country }}</td>
                                <td class="metade2" colspan="3">Naturalidade: {{ $proponent->birthplace }}</td>
                            </tr>
                            <tr>
                                <td class="terco" colspan="2">Telefone Residencial: {{ $proponent->phone }}</td>
                                <td class="terco" colspan="2">Telefone Comercial: {{ $proponent->company_phone }}</td>
                                <td class="terco" colspan="2">Celular: {{ $proponent->cellphone }}</td>
                            </tr>
                            <tr>
                                <td class="metade1" colspan="3">E-Mail: {{ $proponent->email }}</td>
                                <td class="metade2" colspan="3">Profissão: {{ $proponent->occupation }}</td>
                            </tr>
                            <tr>
                                <td class="metade1" colspan="3">Mãe: {{ $proponent->mother_name }}</td>
                                <td class="metade2" colspan="3">Pai: {{ $proponent->father_name }}</td>
                            </tr>
                            <tr>
                                <td class="metade1" colspan="3">Renda Mensal Bruta: R$ {{ formatMoney($proponent->gross_income) }}</td>
                                <td class="metade2" colspan="3">Renda Mensal Líquida: R$ {{ formatMoney($proponent->net_income) }}</td>
                            </tr>
                            @if($proponent->proponent)
                                <tr>
                                    <td colspan="6">Regime de Casamento: {{ $proponent->marriage }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="metade1" colspan="3">Moradia Atual: {{ $proponent->house }}</td>
                                <td class="metade2" colspan="3">Cartório Firma: {{ $proponent->registry }}</td>
                            </tr>
                            @if($proponent->address)
                                <tr>
                                    <td class="metade1" colspan="3">Logradouro: {{ $proponent->address->street }}</td>
                                    <td class="metade2" colspan="3">Número: {{ $proponent->address->number }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Complemento: {{ $proponent->address->complement }}</td>
                                    <td class="metade2" colspan="3">Bairro: {{ $proponent->address->district }}</td>
                                </tr>
                                <tr>
                                    <td class="terco" colspan="2">Município: {{ $proponent->address->city }}</td>
                                    <td class="terco" colspan="2">CEP: {{ $proponent->address->zipcode }}</td>
                                    <td class="terco" colspan="2">UF: {{ $proponent->address->state }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="grey" colspan="6"><b>Dados Profissionais do Proponente {{ $key+1 }}</b></td>
                            </tr>
                            <tr>
                                <td class="terco" colspan="2">Nome: {{ $proponent->company }}</td>
                                <td class="terco" colspan="2">Cargo: {{ $proponent->role }}</td>
                                <td class="terco" colspan="2">Admisssão: {{ formatData($proponent->hired_at) }}</td>
                            </tr>
                            @if($proponent->company_address)
                                <tr>
                                    <td class="metade1" colspan="3">Logradouro: {{ $proponent->company_address->street }}</td>
                                    <td class="metade2" colspan="3">Número: {{ $proponent->company_address->number }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Complemento: {{ $proponent->company_address->complement }}</td>
                                    <td class="metade2" colspan="3">Bairro: {{ $proponent->company_address->district }}</td>
                                </tr>
                                <tr>
                                    <td class="terco" colspan="2">Município: {{ $proponent->company_address->city }}</td>
                                    <td class="terco" colspan="2">CEP: {{ $proponent->company_address->zipcode }}</td>
                                    <td class="terco" colspan="2">UF: {{ $proponent->company_address->state }}</td>
                                </tr>
                            @endif
                            @if($proponent->proponent)
                                <tr>
                                    <td class="grey" colspan="6"><b>Dados do Cônjuge do Proponente {{ $key+1 }}</b></td>
                                </tr>
                                <tr>
                                    <td class="metade2" colspan="3">Nome: {{ $proponent->proponent->name }}</td>
                                    <td class="metade2" colspan="3">Sexo: {{ $proponent->proponent->gender }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Proporção: {{ $proponent->proponent->proportion }} %</td>
                                    <td class="metade2" colspan="3">Data de Nascimento: {{ formatData($proponent->proponent->birthdate) }}</td>
                                </tr>
                                <tr>
                                    <td class="terco" colspan="2">CPF / CNPJ: {{ $proponent->proponent->document }}</td>
                                    <td class="terco" colspan="2">RG: {{ $proponent->proponent->rg }}</td>
                                    <td class="terco" colspan="2">RG Orgão/UF: {{ $proponent->proponent->emitter }}/{{ $proponent->proponent->rg_state }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Nacionalidade: {{ $proponent->proponent->country }}</td>
                                    <td class="metade2" colspan="3">Naturalidade: {{ $proponent->proponent->birthplace }}</td>
                                </tr>
                                <tr>
                                    <td class="terco" colspan="2">Telefone Residencial: {{ $proponent->proponent->phone }}</td>
                                    <td class="terco" colspan="2">Telefone Comercial: {{ $proponent->proponent->company_phone }}</td>
                                    <td class="terco" colspan="2">Celular: {{ $proponent->proponent->cellphone }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">E-Mail: {{ $proponent->proponent->email }}</td>
                                    <td class="metade2" colspan="3">Profissão: {{ $proponent->proponent->occupation }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Mãe: {{ $proponent->proponent->mother_name }}</td>
                                    <td class="metade2" colspan="3">Pai: {{ $proponent->proponent->father_name }}</td>
                                </tr>
                                <tr>
                                    <td class="metade1" colspan="3">Renda Mensal Bruta: R$ {{ formatMoney($proponent->proponent->gross_income) }}</td>
                                    <td class="metade2" colspan="3">Renda Mensal Líquida: R$ {{ formatMoney($proponent->proponent->net_income) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6">Cartório para Registro de Firma: {{ $proponent->proponent->registry }}</td>
                                </tr>
                                <tr>
                                    <td class="grey" colspan="6"><b>Dados Profissionais do Cônjuge do Proponente {{ $key+1 }}</b></td>
                                </tr>
                                <tr>
                                    <td class="terco" colspan="2">Nome: {{ $proponent->proponent->company }}</td>
                                    <td class="terco" colspan="2">Cargo: {{ $proponent->proponent->role }}</td>
                                    <td class="terco" colspan="2">Admisssão: {{ formatData($proponent->proponent->hired_at) }}</td>
                                </tr>
                                @if($proponent->proponent->company_address)
                                    <tr>
                                        <td class="metade1" colspan="3">Logradouro: {{ $proponent->proponent->company_address->street }}</td>
                                        <td class="metade2" colspan="3">Número: {{ $proponent->proponent->company_address->number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="metade1" colspan="3">Complemento: {{ $proponent->proponent->company_address->complement }}</td>
                                        <td class="metade2" colspan="3">Bairro: {{ $proponent->proponent->company_address->district }}</td>
                                    </tr>
                                    <tr>
                                        <td class="terco" colspan="2">Município: {{ $proponent->proponent->company_address->city }}</td>
                                        <td class="terco" colspan="2">CEP: {{ $proponent->proponent->company_address->zipcode }}</td>
                                        <td class="terco" colspan="2">UF: {{ $proponent->proponent->company_address->state }}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                        <tr>
                            <td class="grey text-center" colspan="6"><b>Condições de Pagamento</b></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>Qtd Parcelas</b></td>
                            <td class="text-center"><b>Tipo</b></td>
                            <td class="text-center"><b>Venc 1ª Parcela</b></td>
                            <td class="text-right"><b>Valor da Parcela</b></td>
                            <td class="text-center"><b>%</b></td>
                            <td class="text-right"><b>Total</b></td>
                        </tr>
                        @foreach($proposal->payments as $payment)
                            <tr>
                                <td class="text-center">{{ $payment->quantity }}</td>
                                <td class="text-center">{{ $payment->component }} - {{ $payment->method }}</td>
                                <td class="text-center">{{ formatData($payment->expires_at) }}</td>
                                <td class="text-right">R$ {{ formatMoney($payment->unit_value) }}</td>
                                <td class="text-center">{{ $payment->percentage }} %</td>
                                <td class="text-right">R$ {{ formatMoney($payment->total_value) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-right"><b>Valor do Contrato:</b></td>
                            <td class="text-right">R$ {{ formatMoney($proposal->payments->sum('total_value')) }}</td>
                        </tr>
                        @if($proposal->correction_type)
                            <tr>
                                <td colspan="6">Proposta regida pelo índice de correção monetária {{ $proposal->correction_type }} - {{ @$proposal->monetary_index->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="6">Observações: {{ $proposal->notes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-xs-12 col-sm-12">
                <h5 class="text-justify">O(s) PROPONENTE(s) propõe à VENDEDORA a compra do BEM acima descrito, de acordo com as condições expressas no quadro “Condições de Pagamento’’, desde já autorizam expressamente a pesquisar e informar seu passivo bancário junto ao SCR – Sistema de Informações de Créditos do Banco Central e/ou a Central de Risco de Crédito do Banco Central do Brasil. Autoriza ainda a consulta às fontes indicadas nesta ficha, inclusive junto ao Serasa e Serviços de Proteção ao Crédito.</h5>
                <h5 class="text-justify">Esta proposta submete-se ao disposto no Art. 722 a 729 da Lei n°10.406/02 (Código Civil - Capítulo do Contrato de Corretagem), destacando-se a obrigação da Imobiliária de executar a intermediação com a diligência e a prudência que o negócio requer, prestando espontaneamente, todas as informações sobre o andamento de negociações, especialmente acerca da segurança ou riscos envolvidos, das alterações de valores e do mais que possa influir nos resultados desta incumbência (Art.723).</h5>
                <h5 class="text-justify">Declaro(amos) que fui(fomos) suficientemente informado(s) de todas as condições de pagamento e forma de atualização monetária e juros aplicáveis à aquisição do imóvel objeto desta reserva, às quais foram feitas de conformidade com a tabela em vigor e de tê-las compreendido, não lhe(s) restando nenhuma dúvida.</h5>
                <h5 class="text-justify">Declaro(amos) estar ciente que, nos termos do Código Civil, deverei remunerar a Imobiliária/Corretor pelo equivalente a {{ $proposal->property->block->building->project->comission }}% ({{ convert_number_to_words($proposal->property->block->building->project->comission) }} por cento) do valor total da venda devida a partir da assinatura do contrato de compra e venda.</h5>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-xs-12 col-sm-12">
                <h4>{{ $proposal->property->block->building->project->local }}, <?php echo utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today'))); ?></h4>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-4">
                <hr class="hr">
                <h4 class="text-center">{{ $proposal->main_proponent->name }}</h4>
                <h4 class="text-center">Cliente</h4>
            </div>
            <div class="col-4">
                <hr class="hr">
                <h4 class="text-center">Diretor Comercial</h4>
            </div>
            <div class="col-4">
                <hr class="hr">
                <h4 class="text-center">{{ $proposal->user->name }}</h4>
                <h4 class="text-center">Corretor</h4>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @if(isset($print))
        <script type="text/javascript"> setTimeout(function(){ window.print(); }, 1000); </script>
    @endif
@endsection