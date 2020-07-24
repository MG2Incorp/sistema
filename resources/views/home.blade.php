@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xs-12 col-sm-12">
                <div class="card border-warning mb-4 box">
                    <div class="card-header d-flex" id="heading1">Olá {{ Auth::user()->name }}!</div>
                </div>
            </div>
            <!-- <div class="col-xs-12 col-sm-12">
                <div id="accordion1">
                    <div class="card border-primary mb-4 box">
                        <div class="card-header d-flex" id="heading1">Dashboard <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse1" class="collapse show" aria-labelledby="heading1" data-parent="#accordion1">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-8">
                <div id="accordion2">
                    <div class="card border-danger mb-4 box">
                        <div class="card-header d-flex" id="heading2">Visitantes <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse2" class="collapse show" aria-labelledby="heading2" data-parent="#accordion2">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div id="accordion3">
                    <div class="card border-success mb-4 box">
                        <div class="card-header d-flex" id="heading3">Inventário <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse3" class="collapse show" aria-labelledby="heading3" data-parent="#accordion3">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div id="accordion4">
                    <div class="card border-success mb-4 box">
                        <div class="card-header d-flex" id="heading3">Direct Chat <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse4" class="collapse show" aria-labelledby="heading4" data-parent="#accordion4">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div id="accordion5">
                    <div class="card border-success mb-4 box">
                        <div class="card-header d-flex" id="heading5">Latest Members <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse5" class="collapse show" aria-labelledby="heading3" data-parent="#accordion5">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div id="accordion6">
                    <div class="card border-success mb-4 box">
                        <div class="card-header d-flex" id="heading6">Browser Usage <button class="btn btn-link btn-sm ml-auto p-0 text-dark" data-toggle="collapse" data-target="#collapse6" aria-expanded="true" aria-controls="collapse6"><i class="fas fa-minus"></i></button></div>
                        <div id="collapse6" class="collapse show" aria-labelledby="heading3" data-parent="#accordion6">
                            <div class="card-body h-100">

                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
@endsection
