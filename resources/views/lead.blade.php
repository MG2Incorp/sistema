@extends('layouts.app')
@section('css')
    <style>
        html,body {
            background: url("{{ asset(env('PROJECTS_IMAGES_DIR').$project->background_image) }}") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        .bg_green { background-color: rgb(12,80,59, 0.5); border: none }
        /* .bg_green_in { opacity: 1.0 !important; color: white !important; } */
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
                            <div class="card bg_green text-light">
                                <div class="card-body bg_green_in">
                                    <form action="{{ action('ProjectController@lead_store') }}" method="POST" id="enviar">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                        <div class="form-group text-center">
                                            Digite seus dados abaixo que em breve um de nossos corretores entrará em contato.
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></span>
                                                <input type="text" name="name" class="form-control" placeholder="Nome e Sobrenome">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></span>
                                                <input type="text" name="cellphone" class="form-control celular" placeholder="(19) 9-9999-9999">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-prepend"><span class="input-group-text"><i class="far fa-envelope"></i></span></span>
                                                <input type="email" name="email" class="form-control" placeholder="nome@email.com">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></span>
                                                <span class="input-group-prepend"><span class="input-group-text" id="question"></span></span>
                                                <input id="ans" type="text" class="form-control" name="ans" placeholder="Ex: 6">
                                                <span class="input-group-btn"><button type="reset" value="reset" class="btn btn-info">Atualizar</button></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-block btn-success">Enviar</button>
                                        </div>
                                        @if(session('success'))
                                            <div class="alert alert-success m-0">
                                                {{ session('success') }}
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
    @if($project->chat)
        <!-- BEGIN JIVOSITE CODE {literal} -->
        <script type='text/javascript'>
            (function(){ var widget_id = '{{ $project->chat_code }}';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
        </script>
        <!-- {/literal} END JIVOSITE CODE -->
    @endif
    <script>
        var total;

        function getRandom(){ return Math.ceil(Math.random()* 20); }

        function createSum(){
            var randomNum1 = getRandom(), randomNum2 = getRandom();
            total = randomNum1 + randomNum2;
            $("#question").text(randomNum1+" + "+randomNum2+" = " );
            $("#ans").val('');
            checkInput();
        }

        function checkInput(){
            var input = $("#ans").val(), slideSpeed = 200, hasInput = !!input, valid = hasInput && input == total;
        }

        $(document).ready(function(){
            createSum();
            $('button[type=reset]').click(createSum);
            $("#ans").keyup(checkInput);

            $("#enviar").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cellphone: { required: true, normalizer: function(value) { return $.trim(value); } },
                    email: { email: true, required: true, normalizer: function(value) { return $.trim(value); } },
                    ans: { ans: true, required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });

            jQuery.validator.addMethod("ans", function(value, element) {
                var hasInput = !!value;
                return hasInput && value == total;
            }, "Valor incorreto.");

        });
    </script>
@endsection