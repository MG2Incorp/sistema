<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model{

    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'proposal_id', 'component', 'method', 'quantity', 'expires_at', 'unit_value', 'percentage', 'total_value'
    ];

    public function proposal() {
        return $this->belongsTo('App\Proposal', 'proposal_id');
    }

    public function billings() {
        return $this->hasMany('App\Billing', 'payment_id');
    }

    public function pending_ahead() {
        return $this->hasMany('App\Ahead', 'payment_id')->where('status', 'PENDING')->first();
    }

    public function generateAntecipation($i) {
        $valor_futuro = 0;

        /* VALOR DA ÚLTIMA PARCELA AINDA NÃO PAGA */
        $mensalidade = $this->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->sortByDesc('expires_at')->first()->value;

        /* NUMERO DE PARCELAS QUE VAI ANTECIPAR */
        $periodo = $i;

        /* VALOR DO JUROS CADASTRADO NA PROPOSTA */
        $juros = $this->proposal->tax / 100;

        /* PRIMEIRA PARTE DO CÁLCULO */
        $valor_futuro = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

        /* OBTEM QUANTAS PARCELAS JÁ FORAM ANTECIPADAS */
        $count = 0;
        foreach($this->billings->sortByDesc('expires_at') as $bill) {
            if(in_array($bill->status, [ 'PAID', 'PAID_MANUAL' ])) {
                $count++;
            } else {
                break;
            }
        }
        //PROPOSTA 298 - PRIMEIRAS ANTECIPAÇÕES NÃO BATERAM DEVIDO A FALHA NA FUNÇÃO. CORREÇÃO FEITA EM 13/05/2020

        /* TOTAL DE COBRANÇAS DAQUELE PAGAMENTO - PARCELAS QUE VAI ANTECIPAR - PARCELAS JÁ ANTECIPADAS */
        $periodo = $this->billings->count() - $i - $count;

        $mensalidade = 0;

        /* SEGUNDA PARTE DO CÁLCULO */
        $valor_presente = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

        /* RETORNA VALOR CALCULADO PARA ANTECIPAÇÂO */
        return round($valor_presente, 2);
    }
}
