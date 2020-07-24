@extends('layouts.app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Editar incorporadora</div>
                    <div class="card-body">
                        <form action="{{ action('ConstructorController@update', $constructor->id) }}" method="POST" id="form_constructor">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control" value="{{ $constructor->name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>CNPJ</label>
                                        <input type="text" name="cnpj" class="form-control cnpj" value="{{ $constructor->cnpj }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label>Usuários que receberão e-mails</label>
                                    <select name="users[]" class="form-control js-choice" multiple>
                                        @foreach($constructor->users as $key => $user)
                                            <option value="{{ $user->id }}" {{ $user->receive_emails ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success">Editar incorporadora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('js/choices.js') }}"></script>
    <script>
        $(document).ready(function() {
            const choices = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholder: true, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: '' });
            const choices2 = new Choices('.js-choice2', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar' });


            $("#form_constructor").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cnpj: { required: true, normalizer: function(value) { return $.trim(value); } },
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection