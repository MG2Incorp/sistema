@extends('layouts.app')
@section('content')
    <form action="{{ action('UserController@password') }}" method="POST" id="form_password">
        @csrf
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-1">
                        <div class="card-header">Alterar senha</div>
                        <div class="card-body row">
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Senha atual</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Nova senha</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Confirmar nova senha</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Salvar alterações</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#form_password").validate({
                rules: { confirm_password: { equalTo: "#new_password" } },
                messages: { confirm_password: { equalTo: "As senhas são diferentes." } },
            });
        });
    </script>
@endsection