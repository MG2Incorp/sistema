@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Gerenciar proprietários</div>
                    <div class="card-body">
                        <a href="{{ route('owners.create') }}" class="btn btn-info">Adicionar proprietário</a>
                        <hr>
                        @if($owners->count())
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm m-0 text-center">
                                    <thead>
                                        <tr>
                                            <th width="30%">Proprietário</th>
                                            <!-- <th>Nome Fantasia</th> -->
                                            <th width="60%">PlugBoleto</th>
                                            <!-- <th>Documento</th>
                                            <th>Telefone</th> -->
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($owners as $owner)
                                            <tr>
                                                <td>
                                                    <div>Alias: {{ $owner->alias }}</div>
                                                    <div>{{ $owner->name }}</div>
                                                </td>
                                                <!-- <td>{{ $owner->document }}</td>
                                                <td>{{ $owner->telefone }}</td> -->
                                                <td>
                                                    @if($owner->accounts->count())
                                                        <table class="table table-sm table-bordered m-0 text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th width="50%"></th>
                                                                    <th>Conta</th>
                                                                    <th>Convênio</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($owner->accounts as $account)
                                                                    <tr>
                                                                        <td>{{ getBankCode($account->bank_code) }} | Ag. {{ $account->agency.'-'.$account->agency_dv }} | Num. {{ $account->number.'-'.$account->number_dv }}</td>
                                                                        <td>{{ $account->plugboleto_id }}</td>
                                                                        <td>{{ $account->agreement->plugboleto_id }}</td>
                                                                        <td>
                                                                            @if($account->plugboleto_id && $account->agreement->plugboleto_id)
                                                                                <div class="dropdown dropleft">
                                                                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                        <a class="dropdown-item" href="" data-toggle="modal" data-target="#generate_test_billet" data-id="{{ $account->id }}">Gerar boletos de teste</a>
                                                                                        <a class="dropdown-item" href="" data-toggle="modal" data-target="#view_test_billet" data-id="{{ $account->id }}">Visualizar boletos de teste</a>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown dropleft">
                                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item" href="{{ route('owners.edit', $owner->id) }}">Editar</a>
                                                            <!-- <a class="dropdown-item" href="{{ route('owners.delete', $owner->id) }}">Remover</a> -->
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h5 class="text-center m-0">Nenhum proprietário cadastrado.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generate_test_billet" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_generate_test_billet" action="{{ action('BillingController@billet_test_generate') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Gerar boletos de teste</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_generate_test_billet"></div>
                <div class="modal-footer row justify-content-center">
                    <div class="col-12 col-sm-5">
                        <button type="submit" class="btn btn-success btn-block" id="btn_paid_manual">Gerar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="view_test_billet" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="form_view_test_billet" action="{{ action('BillingController@billet_remessa') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Visualizar boletos de teste</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_view_test_billet"></div>
                <div class="modal-footer row justify-content-center">
                    <div class="col-12 col-sm-5">
                        <button type="submit" class="btn btn-success btn-block" id="btn_generate_remessa">Gerar arquivo de remessa com selecionados</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#generate_test_billet').on('show.bs.modal', function (event) {
                $("#content_generate_test_billet").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.billet.test') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_generate_test_billet").html(data);
                        $('.money').maskMoney({ thousands: '.', decimal: ',', allowZero: true });
                    }
                });
            });

            $('#view_test_billet').on('show.bs.modal', function (event) {
                $("#content_view_test_billet").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border rounded-circle" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('billing.billet.test.view') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_view_test_billet").html(data);
                    }
                });
            });

            $("#form_generate_test_billet").validate();
            // $("#form_view_test_billet").validate();
        });
    </script>
@endsection