<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billet extends Model {

    use SoftDeletes;

    protected $table = 'billets';

    protected $fillable = [
        'billetable_type',
        'billetable_id',
        'token',
        'emitted_at',
        'bar_code',
        'status',
        'idIntegracao',
        'impressao',
        'PagamentoData',
        'PagamentoRealizado',
        'PagamentoValorPago',
        'id_baixa',
        'email_sent_protocol',
        'remessa_gerada'
    ];

    public function billetable() {
        return $this->morphTo();
    }

    public function billing() {
        return $this->belongsTo('App\Billing', 'billing_id');
    }

    public function errors() {
        return $this->hasMany('App\BilletError', 'billet_id');
    }

    public function statuses() {
        return $this->hasMany('App\BilletHistoryStatus', 'billet_id');
    }

    public function setStatus($status) {
        $this->update([ 'status' => $status ]);

        if(!$this->statuses->count() || $this->statuses->sortByDesc('created_at')->first()->status != $status) {
            \App\BilletHistoryStatus::create([ 'billet_id' => $this->id, 'status' => $status ]);
        }

        // \Log::info('Boleto Verificado: '.$this->token.'SET STATUS DENTRO');

        if($status == 'EMITIDO' && !$this->remessa_gerada) {
            try {
                $api = new \App\Api\PlugBoleto();
                $solicitacao = $api->gerarRemessaUnica($this);
                // \Log::info(serialize($solicitacao));

                if(isset($solicitacao->_dados->_sucesso[0]->arquivo) && $solicitacao->_dados->_sucesso[0]->arquivo) {
                    $this->update([ 'remessa_gerada' => $solicitacao->_dados->_sucesso[0]->arquivo ]);
                }
            } catch (\Exception $e) {
                logging($e);
            }
        }

        if($status == 'REGISTRADO' && !$this->email_sent_protocol && $this->billetable_type != 'App\BillingTest') {
            \Log::info("SOLICITADO ENVIO DE EMAIL: ".$this->id);

            try {
                $api = new \App\Api\PlugBoleto();
                $solicitacao = $api->solicitarEmail($this);

                if(isset($solicitacao->_dados->protocolo) && $solicitacao->_dados->protocolo) $this->update([ 'email_sent_protocol' => $solicitacao->_dados->protocolo ]);

                \Log::info(serialize($solicitacao));
            } catch (\Exception $e) {
                logging($e);
            }
        }
    }
}
