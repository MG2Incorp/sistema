<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    private $data = array();

    public function __construct() {
        $this->middleware('auth:client');
    }

    public function index() {
        $this->data['contracts'] = \App\Proposal::whereHas('statuses', function($query) {
            return $query->where('status', 'SOLD');
        })->whereHas('all_proponents', function($query) {
            return $query->where('client_id', auth()->guard('client')->user()->id);
        })->get();

        return view('clients.home', $this->data);
    }

    public function contract(Request $request) {

        if(!$request->has('contract')) return redirect()->back()->with('error', 'Não foi possível completar a operação.');
        if(!$proposal = \App\Proposal::find($request->contract)) return redirect()->back()->with('error', 'Contrato não encontrado.');
        if(!$proposal->statuses->pluck('status')->contains('SOLD')) return redirect()->back()->with('error', 'Contrato inválido.');
        if(!$proposal->all_proponents->pluck('client_id')->contains(auth()->guard('client')->user()->id)) return redirect()->back()->with('error', 'Contrato inválido.');

        $this->data['proposal'] = $proposal;

        $this->data['billings'] = $billings = \App\Billing::whereIn('payment_id', $proposal->payments->pluck('id')->toArray())->get()->groupBy('year')->map(function($item, $key) {
            return $item->groupBy('month')->sortKeys();
        })->sortKeys();

        $this->data['aheads'] = $aheads = \App\Billing::whereIn('payment_id', $proposal->payments->pluck('id')->toArray())->get()->groupBy('payment_id')->map(function($item, $key){
            return $item->groupBy('year')->map(function($item2, $key2) {
                return $item2->groupBy('month')->sortKeys();
            })->sortKeys();
        });

        return view('clients.contract', $this->data);
    }
}
