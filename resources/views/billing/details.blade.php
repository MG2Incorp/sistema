@extends('layouts.app')
@section('content')
    <?php $caption_colors = [ '#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4', '#009688', '#4CAF50', '#CDDC39', '#FFEB3B', '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B' ]; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-3 mb-4">
                <div class="card h-100">
                    <div class="card-header text-center">Valor do Imóvel</div>
                    <div class="card-body text-center">
                        <h3 class="font-weight-bold">R$ {{ formatMoney($proposal->property->value) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-3 mb-4">
                <div class="card h-100">
                    <div class="card-header text-center">Valor da Proposta</div>
                    <div class="card-body text-center">
                        <h3 class="font-weight-bold">R$ {{ formatMoney($proposal->payments->sum('total_value')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-3 mb-4">
                <div class="card h-100">
                    <div class="card-header text-center">Valor Final Previsto</div>
                    <div class="card-body text-center">
                        <h3 class="font-weight-bold">R$ {{ formatMoney($proposal->billings->where('status', 'PENDING')->sum('value') + $proposal->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->sum('paid_value')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-3 mb-4">
                <div class="card h-100">
                    <div class="card-header text-center">Valor Pago até o Momento</div>
                    <div class="card-body text-center">
                        <h3 class="font-weight-bold">R$ {{ formatMoney($proposal->billings->sum('paid_value')) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Informações Principais da Proposta</div>
                    <div class="card-body">
                        <div><b>Empreendimento: </b>{{ $proposal->property->block->building->project->name }}</div>
                        <div><b>Prédio: </b>{{ $proposal->property->block->building->name }}</div>
                        <div><b>Quadra/Andar: </b>{{ $proposal->property->block->label }}</div>
                        <div><b>Imóvel: </b>{{ $proposal->property->number }}</div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Proponentes da Proposta</div>
                    <div class="card-body">
                        @foreach($proposal->proponents as $proponent)
                            <div>{{ $proponent->name }} @if($proponent->proponent) / {{ $proponent->proponent->name }} @endif</div>
                        @endforeach
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Condições de Pagamento conforme Proposta</div>
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <form action="{{ action('BillingController@generate') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info">Gerar cobranças</button>
                                </div>
                            </form>
                            <div class="form-group">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#billing_total_ahead" data-proposal="{{ $proposal->id }}">Gerar quitação completa</button>
                            </div>
                        </div>
                        <table class="table table-sm table-bordered text-center m-0">
                            <thead>
                                <tr>
                                    <th>Legenda</th>
                                    <th>Qtd Parcelas</th>
                                    <th>Carnê</th>
                                    <th>Componente</th>
                                    <th>Método</th>
                                    <th>Valor da Parcela</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $captions = []; ?>
                                @foreach($proposal->payments as $key => $payment)
                                    <?php
                                        $captions[$payment->id] = $caption_colors[$key];

                                        $carne = str_pad($payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count(), strlen($payment->billings->count()), '0', STR_PAD_LEFT)."/".$payment->billings->count();
                                    ?>
                                    <tr>
                                        <td><h4 class="m-0"><span class="badge rounded-circle"><i style="color: {{ @$captions[$payment->id] }}" class="fas fa-circle"></i></span></h4></td>
                                        <td>{{ $payment->quantity }}</td>
                                        <td>{{ $carne }}</td>
                                        <td>{{ $payment->component }}</td>
                                        <td>{{ $payment->method }}</td>
                                        <td>R$ {{ formatMoney($payment->unit_value) }}</td>
                                        <td>R$ {{ formatMoney($payment->total_value) }}</td>
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#billing_ahead_new" data-payment="{{ $payment->id }}">Antecipação</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Cobranças Emitidas</div>
                    <div class="card-body">
                        @if(count($billings))
                            <div class="card text-center">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs nav-fill">
                                        @foreach($billings as $year => $billing)
                                            <li class="nav-item"><a class="nav-link {{ date('Y') == $year ? 'active' : '' }}" data-toggle="tab" href="#year-{{ $year }}" role="tab">{{ $year }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        @foreach($billings as $year => $billing)
                                            <div class="tab-pane {{ date('Y') == $year ? 'active' : '' }}" id="year-{{ $year }}" role="tabpanel">
                                                <div class="card text-center">
                                                    <div class="card-header">
                                                        <ul class="nav nav-tabs card-header-tabs nav-fill">
                                                            @foreach($billing as $month => $bill)
                                                                <li class="nav-item"><a class="nav-link {{ date('Y') == $year && date('m') == $month ? 'active' : '' }}" data-toggle="tab" href="#year-{{ $year }}-month-{{ $month }}" role="tab">{{ getMonthsAbbr2()[$month] }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="tab-content">
                                                            @foreach($billing as $month => $bill)
                                                                <div class="tab-pane {{ date('Y') == $year && date('m') == $month ? 'active' : '' }}" id="year-{{ $year }}-month-{{ $month }}" role="tabpanel">
                                                                    <table class="table table-sm table-bordered text-center m-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="10%">Componente</th>
                                                                                <th>Token</th>
                                                                                <th width="12%">Vencimento</th>
                                                                                <th width="15%">Método</th>
                                                                                <th width="10%">Valor Original</th>
                                                                                <th width="10%">Valor Pago</th>
                                                                                <th width="15%">Status</th>
                                                                                <th width="8%"></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($bill->sortBy('expires_at') as $b)
                                                                                <?php $status = getBillingStatusLayout($b->status); ?>
                                                                                <tr>
                                                                                    <td><span class="badge rounded-circle"><i style="color: {{ @$captions[$b->payment_id] }}" class="fas fa-circle"></i></span></td>
                                                                                    <td>{{ $b->token }}</td>
                                                                                    <td>{{ formatData($b->expires_at) }}</td>
                                                                                    <td>{{ $b->payment->method }}</td>
                                                                                    <td>R$ {{ formatMoney($b->value) }}</td>
                                                                                    <td>R$ {{ formatMoney($b->paid_value) }}</td>
                                                                                    <td class="text-{{ $status['text'] }} font-weight-bold"><i class="{{ $status['icon'] }}"></i> {{ $status['content'] }}</td>
                                                                                    <td>
                                                                                        <div class="dropdown dropleft">
                                                                                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                <h6 class="dropdown-header font-weight-bold text-danger">Cobrança</h6>
                                                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#billing_details" data-billing="{{ $b->id }}">Detalhes</a>
                                                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#billing_paid_manual" data-billing="{{ $b->id }}">Baixa manual</a>
                                                                                                @if($b->status == 'PENDING' && $b->expires_at <= Carbon\Carbon::now()->addMonth())
                                                                                                @endif
                                                                                                @if($b->status == 'PAID_MANUAL')
                                                                                                    <a class="dropdown-item" href="{{ route('billing.paid.manual.receipt') }}?billing={{ $b->token }}">Baixar recibo</a>
                                                                                                @endif
                                                                                                @if($b->billet_generated())
                                                                                                    <div class="dropdown-divider"></div>
                                                                                                    @if($b->billet_generated()->id_baixa)
                                                                                                        <a class="dropdown-item" href="{{ route('billing.billet.baixa') }}?billet={{ $b->billet_generated()->token }}" target="_BLANK">Consultar baixa</a>
                                                                                                    @endif
                                                                                                    <a class="dropdown-item" href="{{ route('billing.billet.email.sent') }}?billet={{ $b->billet_generated()->token }}">Solicitar re-envio de e-mail</a>
                                                                                                    <a class="dropdown-item" href="{{ route('billing.billet') }}?billet={{ $b->billet_generated()->token }}" target="_BLANK">Visualizar boleto</a>
                                                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#send_billet_whatsapp" data-billing="{{ $b->id }}">Enviar boleto pelo WhatsApp</a>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <h5 class="text-center m-0">Nenhuma cobrança encontrada.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="billing_details" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_billing_details"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="billing_paid_manual" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_paid_manual" action="{{ action('BillingController@paid_manual_payment') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Realizar baixa manual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_billing_paid_manual"></div>
                <div class="modal-footer row justify-content-center">
                    <div class="col-12 col-sm-5">
                        <button type="submit" class="btn btn-success btn-block" id="btn_paid_manual">Realizar a baixa manual</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="billing_ahead" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_ahead" action="{{ action('BillingController@ahead_payment') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Antecipação de parcelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_billing_ahead"></div>
                <div class="modal-footer row justify-content-center">
                    <div class="col-12 col-sm-5">
                        <button type="submit" class="btn btn-success btn-block" id="btn_ahead">Gerar antecipação</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="billing_ahead_new" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_ahead_new" action="{{ action('BillingController@ahead_payment') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Antecipação de parcelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_billing_ahead_new"></div>
                <div class="modal-footer row justify-content-center" id="footer_billing_ahead_new"></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="billing_total_ahead" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_billing_total_ahead" action="{{ action('BillingController@ahead_total_generate') }}">
                {{ csrf_field() }}
                <input type="hidden" name="proposal" value="{{ $proposal->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Gerar quitação total</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_billing_total_ahead"></div>
                <div class="modal-footer row justify-content-center">
                    <div class="col-12 col-sm-5">
                        <button type="submit" class="btn btn-success btn-block" id="btn_billing_total_ahead">Gerar quitação total</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="send_billet_whatsapp" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar boleto pelo WhatsApp</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_send_billet_whatsapp"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {

            $('#billing_details').on('show.bs.modal', function (event) {
                $("#content_billing_details").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                var button = $(event.relatedTarget);
                var id = button.data('billing');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.billing') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_billing_details").html(data);
                    }
                });
            });

            $('#billing_paid_manual').on('show.bs.modal', function (event) {
                $("#content_billing_paid_manual").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                var button = $(event.relatedTarget);
                var id = button.data('billing');
                $("#btn_paid_manual").prop('disabled', true);

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.paid.manual.value') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_billing_paid_manual").html(data);
                        $('.money').maskMoney({ thousands: '.', decimal: ',', allowZero: true });
                        $("#btn_paid_manual").prop('disabled', false);
                    }
                });
            });

            $('#send_billet_whatsapp').on('show.bs.modal', function (event) {
                $("#content_send_billet_whatsapp").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                var button = $(event.relatedTarget);
                var id = button.data('billing');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.billet.send.whatsapp') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_send_billet_whatsapp").html(data);
                    }
                });
            });

            $(document).on('blur', '.calculate', function() {
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.paid.manual.calculate') }}", type: "POST", data: { id: $("#billing_id").val(), billet: $("#billet_value").val(), discount: $("#discount_value").val() }, cache: false, processData: true,
                    success: function(data) {
                        $("#acrescimo").html(data['acrescimo']);
                        $("#pago").html(data['pago']);
                    }
                });
            });

            $('#billing_ahead').on('show.bs.modal', function (event) {
                $("#content_billing_ahead").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                var button = $(event.relatedTarget);
                var id = button.data('billing');
                $("#btn_ahead").prop('disabled', true);

                var array = new Array();
                $(".checkbox_ahead").each(function() {
                    if($(this).is(":checked")) array.push($(this).val());
                });

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.ahead.value') }}", type: "POST", data: { array: array }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_billing_ahead").html(data);
                        $("#btn_ahead").prop('disabled', false);
                    }
                });
            });

            $('#billing_ahead_new').on('show.bs.modal', function (event) {
                $("#content_billing_ahead_new").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                $("#footer_billing_ahead_new").html('');

                var button = $(event.relatedTarget);
                var id = button.data('payment');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.ahead.value') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_billing_ahead_new").html(data);
                    }
                });
            });

            $(document).on('click', '#generate_antecipation', function(event) {
                var id = $(this).data('payment');
                var qtd = $("#qtd_antecipation").val();

                $("#content_billing_ahead_new").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                $("#footer_billing_ahead_new").html('');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.ahead.value') }}", type: "POST", data: { id: id, qtd: qtd }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_billing_ahead_new").html(data);
                        $("#footer_billing_ahead_new").html('<div class="col-12 col-sm-5"><button type="submit" class="btn btn-success btn-block" id="btn_ahead">Gerar antecipação</button></div>');
                    }
                });
            });

            $('#billing_total_ahead').on('show.bs.modal', function (event) {
                $("#content_billing_total_ahead").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');
                var button = $(event.relatedTarget);
                var id = button.data('proposal');
                $("#btn_billing_total_ahead").prop('disabled', true);

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.ahead.total.value') }}", type: "POST", data: { proposal: id }, cache: false, processData: true,
                    success: function(data) {
                        if(data['success']) {
                            $("#content_billing_total_ahead").html(data['success']);
                            $("#btn_billing_total_ahead").prop('disabled', false);
                        } else {
                            $("#content_billing_total_ahead").html(data['error']);
                        }
                    }
                });
            });

            $("#form_paid_manual").validate();
            $("#form_ahead").validate();
            $("#form_ahead_new").validate();
            $("#form_billing_total_ahead").validate();
        });
    </script>
@endsection