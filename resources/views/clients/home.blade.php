@extends('layouts.site')
@section('css')
    <link href="{{ asset('css/flatpickr.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-4 px-0 mx-4">Meus contratos</div>
                    <div class="card-header py-4 px-0 mx-4">
                        <!-- <div class="row justify-content-sm-between align-items-sm-center">
                            <div class="col-md-5 col-lg-4 mb-2 mb-md-0">
                                <div id="datepickerWrapper" class="js-focus-state u-datepicker w-auto input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-calendar"></span></span></div>
                                    <input type="text" class="js-range-datepicker form-control bg-white rounded-right" data-rp-wrapper="#datepickerWrapper" data-rp-type="range" data-rp-date-format="d M Y" data-rp-default-date='["05 Jul 2018", "19 Jul 2018"]' data-rp-is-disable-future-dates="true">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <select id="datatableEntries" class="js-select selectpicker dropdown-select" data-width="fit" data-style="btn-soft-primary btn-sm">
                                            <option value="6">6 entries</option>
                                            <option value="12">12 entries</option>
                                            <option value="18">18 entries</option>
                                            <option value="24">24 entries</option>
                                        </select>
                                    </div>
                                    <div class="js-focus-state input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text" id="searchActivities"><span class="fas fa-search"></span></span></div>
                                        <input id="datatableSearch" type="email" class="form-control" placeholder="Search activities" aria-label="Search activities" aria-describedby="searchActivities">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="card-body p-4">
                        <div class="table-responsive-md">
                            <table class="js-datatable table table-borderless m-0 text-center">
                                <thead>
                                    <tr class="text-uppercase font-size-1">
                                        <th scope="col" class="font-weight-medium">Empreendimento</th>
                                        <th scope="col" class="font-weight-medium">Imóvel</th>
                                        <th scope="col" class="font-weight-medium">Status</th>
                                        <th scope="col" class="font-weight-medium">Valor Contrato</th>
                                        <th scope="col" class="font-weight-medium">Valor Previsto</th>
                                        <th scope="col" class="font-weight-medium">Valor Pago</th>
                                        <th scope="col" class="font-weight-medium">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="font-size-1">
                                    @foreach($contracts as $contract)
                                        <?php
                                            $status = $contract->getContractStatus();
                                            $info = getContractStatusLayout($status);
                                        ?>
                                        <tr>
                                            <td class="align-middle text-secondary">{{ $contract->property->block->building->project->name }}</td>
                                            <td class="align-middle text-secondary">
                                                <div>Bloco: {{ $contract->property->block->building->name }}</div>
                                                <div>Quadra/Andar: {{ $contract->property->block->label }}</div>
                                                <div>Número: {{ $contract->property->number }}</div>
                                            </td>
                                            <td class="align-middle text-{{ $info['text'] }}"><i class="{{ $info['icon'] }}"></i> {{ $info['content'] }}</td>
                                            <td class="align-middle text-secondary">R$ {{ formatMoney($contract->payments->sum('total_value')) }}</td>
                                            <td class="align-middle text-secondary">R$ {{ formatMoney($contract->billings->sum('value')) }}</td>
                                            <td class="align-middle text-secondary">R$ {{ formatMoney($contract->billings->sum('paid_value')) }}</td>
                                            <td class="align-middle text-secondary"><a href="{{ route('client.contract') }}?contract={{ $contract->id }}">Ver detalhes</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/core.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script src="{{ asset('js/select.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/selectpicker.js') }}"></script>
    <script>
        $(document).on('ready', function () {
            $.HSCore.components.HSRangeDatepicker.init('.js-range-datepicker');
            $.HSCore.components.HSSelectPicker.init('.js-select');
        });
    </script>
@endsection