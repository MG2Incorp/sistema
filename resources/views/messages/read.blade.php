@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            @if($messages->count())
                <div class="col-12">
                    <h4 class="text-center mb-4">Você tem mensagens não lidas</h4>
                </div>
                <div class="col-12">
                    @foreach($messages as $message)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="card-title">De: {{ $message->from->name }}</h4>
                                <p class="card-text">Mensagem: {{ $message->message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-12" align="center">
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" name="read" value="1">
                        <button class="btn btn-success">Marcas todas como lidas</button>
                    </form>
                </div>
            @else
                <div class="col-12">
                    <h4 class="text-center mb-4">Você não tem mensagens não lidas.</h4>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
@endsection