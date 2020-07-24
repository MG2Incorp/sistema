<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class ClientLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $guard = 'client';

    // protected $redirectTo = '/home';
    protected $redirectTo = '/';

    public function __construct() {
        $this->middleware('guest:client')->except('logout');
    }

    public function showLoginForm() {
        $this->data['hide'] = true;
        return view('clients.login', $this->data);
    }

    public function login(Request $request) {
        if (auth()->guard('client')->attempt(['document' => onlyNumber($request->cpf), 'password' => $request->password])) {
            // dd(auth()->guard('client')->user());
            // return redirect()->intended(route('client'));
            return redirect()->route('client');
        }
        return back()->withErrors(['cpf' => 'Credenciais não encontradas.']);
    }

    public function logout() {
        auth()->guard('client')->logout();

        return redirect()->route('client.login.show');
    }
}