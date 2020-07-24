<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Message;
use App\User;

use Auth;
use Exception;
use Log;

use Carbon\Carbon;

class MessageController extends Controller
{
    private $data = array();

    public function index(Request $request) {
        $this->data['enviadas'] = Auth::user()->messages_sent->sortByDesc('created_at');
        $this->data['recebidas'] = Auth::user()->messages_received->sortByDesc('created_at');

        return view('messages.index', $this->data);
    }

    public function send(Request $request) {
        if (!$request->has('users') && !$request->has('all_users')) return redirect()->back()->with('error', 'Nenhum destinatário foi selecionado.')->withInput();

        $send = 1;

        if($request->has('all_users')) {
            foreach ($request->all_users as $key => $all) {

                if(!$project = \App\Project::find($key)) continue;
                if(!\Auth::user()->projects->contains('id', $key)) continue;

                if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) {
                    if($project->users->where('id', '!=', \Auth::user()->id)->count()) {
                        foreach($project->users->where('id', '!=', \Auth::user()->id) as $user) {

                            $usuario = User::find($user->id);

                            try {
                                if(in_array(\Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) {
                                    $message = Message::create([
                                        'sender'    => Auth::user()->id,
                                        'receiver'  => $user->id,
                                        'message'   => $request->message,
                                        'read_at'   => null
                                    ]);
                                } else {
                                    if(in_array($usuario->role, ['ADMIN', 'INCORPORATOR'])) {
                                        $message = Message::create([
                                            'sender'    => Auth::user()->id,
                                            'receiver'  => $user->id,
                                            'message'   => $request->message,
                                            'read_at'   => null
                                        ]);
                                    } else {
                                        $send = 0;
                                    }
                                }
                            } catch (Exception $e) {
                                logging($e);
                            }

                        }
                    }
                } else {
                    $has = 0;
                    if($project->users->where('id', '!=', Auth::user()->id)->count()) {
                        foreach($project->users->where('id', '!=', Auth::user()->id) as $user) {
                            if(in_array($user->role, ['ADMIN', 'INCORPORATOR'])) {
                                $has = 1;
                                break;
                            }
                        }
                    }

                    if($has) {
                        foreach($project->users->where('id', '!=', Auth::user()->id) as $user) {
                            if($user->checkPermission($project->id, ['ADMIN', 'COORDINATOR'])) {

                                $usuario = User::find($user->id);

                                try {
                                    if(in_array(\Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) {
                                        $message = Message::create([
                                            'sender'    => Auth::user()->id,
                                            'receiver'  => $user->id,
                                            'message'   => $request->message,
                                            'read_at'   => null
                                        ]);
                                    } else {
                                        if(in_array($usuario->role, ['ADMIN', 'INCORPORATOR'])) {
                                            $message = Message::create([
                                                'sender'    => Auth::user()->id,
                                                'receiver'  => $user->id,
                                                'message'   => $request->message,
                                                'read_at'   => null
                                            ]);
                                        } else {
                                            $send = 0;
                                        }
                                    }
                                } catch (Exception $e) {
                                    logging($e);
                                }

                            }
                        }
                    }
                }
            }
        }

        if($request->has('users')) {
            foreach ($request->users as $key => $users) {
                foreach ($users as $user) {
                    if ($user != Auth::user()->id) {
                        $usuario = User::find($user);

                        try {
                            if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) {
                                $message = Message::create([
                                    'sender'    => Auth::user()->id,
                                    'receiver'  => $user,
                                    'message'   => $request->message,
                                    'read_at'   => null
                                ]);
                            } else {
                                if(in_array($usuario->role, ['ADMIN', 'INCORPORATOR'])) {
                                    $message = Message::create([
                                        'sender'    => Auth::user()->id,
                                        'receiver'  => $user,
                                        'message'   => $request->message,
                                        'read_at'   => null
                                    ]);
                                } else {
                                    $send = 0;
                                }
                            }
                        } catch (Exception $e) {
                            logging($e);
                        }
                    }
                }
            }
        }

        if ($send) return redirect()->route('messages.index')->with('success', 'Mensagens enviadas com sucesso.');

        return redirect()->route('messages.index')->with('error', 'As mensagens não puderiam ser enviadas.');
    }

    public function read(Request $request) {
        if ($request->has('read')) {
            if (Auth::user()->messages_not_read->count()) {
                foreach (Auth::user()->messages_not_read as $key => $message) {
                    $message->read_at = Carbon::now()->toDateTimeString();
                    $message->save();
                }

                return redirect()->route('home')->with('success', 'Você leu todas as mensagens pendentes.');
            }
        }

        $this->data['messages'] = Auth::user()->messages_not_read;

        if(Auth::user()->messages_not_read->count()) $this->data['hide'] = true;

        return view('messages.read', $this->data);
    }
}
