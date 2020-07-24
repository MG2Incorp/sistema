@extends('layouts.app')
@section('css')
    <style>
        html,body { background: linear-gradient(180deg, #fff 50%, #f5b920 50%); }
        .bg_white_in { padding:25px !important;-moz-box-shadow: 0px 1px 5px #a3a1a3;-webkit-box-shadow: 0px 1px 5px #a3a1a3;box-shadow: 0px 1px 5px #a3a1a3; background-color: white !important }
        .m_0 { margin-bottom: 0 !important }
        .full-height { height: 100vh; }
        .flex-center { align-items: center; display: flex; justify-content: center;}
    </style>
@endsection
@section('content')
    <div class="full-height">
        <div class="container full-height">
            <div class="row full-height">
                <div class="col-12">
                    <div class="row full-height flex-center justify-content-center">
                        <div class="col-12 col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="my-4" align="center">
                                        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}"></a>
                                    </div>
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="GET" action="" id="calcular">
                                        <input type="hidden" name="empreendimento" value="{{ $empreendimento }}">
                                        <div class="form-group text-center">
                                            Simule abaixo o valor do investimento
                                        </div>
                                        <div class="form-group">
                                            <label>Lote</label>
                                            <select name="lote" id="lote" class="form-control">
                                                <option value="">Selecione...</option>
                                                @foreach($properties as $property)
                                                    @php $min = $property->block->building->project->minimum_percentage/100;  @endphp
                                                    <option {{ isset($lote) && $lote == $property->id ? 'selected' : '' }} value="{{ $property->id }}" data-min-f="R$ {{ formatMoney($property->value*$min) }}" data-min="{{ $property->value*$min }}">{{ @$property->block->building->project->name }} - {{ @$property->block->building->name }} - {{ @$property->block->label }} - {{ @$property->number }} - R$ {{ @formatMoney($property->value) }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="feedback">Valor mínimo da entrada: <span id="valor_minimo"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-7">
                                                <div class="form-group">
                                                    <label>Entrada</label>
                                                    <div class="input-group">
                                                        <span class="input-group-prepend"><span class="input-group-text">R$</span></span>
                                                        <input type="text" name="entrada" id="entrada" class="form-control money" placeholder="Ex: 100,00" value="{{ isset($entrada) ? $entrada : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Parcelas</label>
                                                    <input type="text" name="parcelas" id="parcelas" class="form-control" placeholder="Ex: 200" value="{{ isset($parcelas) ? $parcelas : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-block btn-success">Calcular</button>
                                        </div>
                                        @if(isset($lote))
                                            <div class="text-center">
                                                @if(isset($up))
                                                    <h5>Valor da parcela</h5>
                                                    <h2 class="text-success">R$ 0,00</h2>
                                                @endif
                                                @if(isset($down))
                                                    <h5 class="text-danger">Valor da entrada inválido.</h5>
                                                @endif
                                                @if(isset($ok))
                                                    <h5>Valor da parcela</h5>
                                                    <h2 class="text-success">{{ $ok }}</h2>
                                                @endif
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function check() {
            var min = parseFloat($("#lote option:selected").attr('data-min'));
            var ent = $("#entrada").val().replace(/\./g, '').replace(',', '.');
            var min_f = $("#lote option:selected").attr('data-min-f');

            if (ent < min) {
                $("#valor_minimo").text(min_f)
                $("#feedback").show();

                return false;
            }

            return true;
        }

        $(document).ready(function(){
            $(document).on('blur', '#entrada', function(){


            });

            $.validator.addMethod("minimium", function(value, element) {
                return check();
            }, 'Valor de entrada inválido');

            $("#calcular").validate({
                rules: {
                    lote: { required: true, normalizer: function(value) { return $.trim(value); } },
                    entrada: { minimium: true, required: true, normalizer: function(value) { return $.trim(value); } },
                    parcelas: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection