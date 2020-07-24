@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form action="{{ action('MessageController@send') }}" method="POST">
                    @csrf
                    <div class="accordion" id="accordion">
                        <div class="card">
                            <div class="card-header" id="heading1" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">Nova mensagem</div>
                            <div id="collapse1" class="collapse show" aria-labelledby="heading1" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-sm-9">
                                            <textarea name="message" class="form-control" rows="3" style="resize: none"></textarea>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#receivers">Selecionar destinatários</button>
                                            <button class="btn btn-success btn-block">Enviar mensagem</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="heading2" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">Mensagens enviadas</div>
                            <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
                                <div class="card-body">
                                    @if($enviadas->count())
                                        <table class="table m-0 table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Informações</th>
                                                    <th>Mensagem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($enviadas as $message)
                                                    <tr>
                                                        <td>
                                                            <div>Destinatário: {{ $message->to->name }}</div>
                                                            <div>Data de Envio: {{ dateTimeString($message->created_at) }} </div>
                                                            <div>Data de Leitura: {{ dateTimeString($message->read_at) }}</div>
                                                        </td>
                                                        <td>{{ $message->message }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <h6 class="text-center m-0 p-0">Você ainda não enviou nenhuma mensagem.</h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="heading3" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">Mensagens recebidas</div>
                            <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
                                <div class="card-body">
                                    @if($recebidas->count())
                                        <table class="table m-0 table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Informações</th>
                                                    <th>Mensagem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recebidas as $message)
                                                    <tr>
                                                        <td>
                                                            <div>Destinatário: {{ $message->from->name }}</div>
                                                            <div>Data de Envio: {{ dateTimeString($message->created_at) }} </div>
                                                            <div>Data de Leitura: {{ dateTimeString($message->read_at) }}</div>
                                                        </td>
                                                        <td>{{ $message->message }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <h6 class="text-center">Você ainda não recebeu nenhuma mensagem.</h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="receivers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Selecione os destinatários de sua mensagem</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    @foreach(Auth::user()->projects as $key => $project)
                                        @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                            <div class="form-group">
                                                <label>{{ $project->name }}</label>
                                                @if($project->users->where('id', '!=', Auth::user()->id)->count())
                                                    <div class="form-check float-right">
                                                        <input class="form-check-input check_all" type="checkbox" name="all_users[{{ $project->id }}]" value="{{ $key }}" id="defaultCheck{{ $key }}">
                                                        <label class="form-check-label" for="defaultCheck{{ $key }}">Enviar para todos desse empreendimento</label>
                                                    </div>
                                                    <select class="form-control js-choice select_users" name="users[{{ $project->id }}][]" multiple id="select_users{{ $key }}">
                                                        @foreach($project->users->where('id', '!=', Auth::user()->id) as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <h6>Você não tem permissão para enviar mensagens para nenhum usuário desse empreendimento.</h6>
                                                @endif
                                            </div>
                                        @else
                                            @php $has = 0; @endphp
                                            @if($project->users->where('id', '!=', Auth::user()->id)->count())
                                                @foreach($project->users->where('id', '!=', Auth::user()->id) as $user)
                                                    @if(in_array($user->role, ['ADMIN', 'INCORPORATOR']))
                                                        @php $has = 1; break; @endphp
                                                    @endif
                                                @endforeach
                                            @endif

                                            <div class="form-group">
                                                <label>{{ $project->name }}</label>
                                                @if($has)
                                                    <div class="form-check float-right">
                                                        <input class="form-check-input check_all" type="checkbox" name="all_users[{{ $project->id }}]" value="{{ $key }}" id="defaultCheck{{ $key }}">
                                                        <label class="form-check-label" for="defaultCheck{{ $key }}">Enviar para todos desse empreendimento</label>
                                                    </div>
                                                    <select class="form-control js-choice select_users" name="users[{{ $project->id }}][]" multiple id="select_users{{ $key }}">
                                                        @foreach($project->users->where('id', '!=', Auth::user()->id) as $user)
                                                            @if($user->checkPermission($project->id, ['ADMIN', 'COORDINATOR']))
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <h6>Você não tem permissão para enviar mensagens para nenhum usuário desse empreendimento.</h6>
                                                @endif
                                            </div>
                                        @endif
                                        <hr>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/choices.js') }}"></script>
    <script>
        $(document).ready(function(){
            const choices2 = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione os usuários...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });
        });
    </script>
@endsection