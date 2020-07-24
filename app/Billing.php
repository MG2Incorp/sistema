<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model {

    use SoftDeletes;

    protected $table = 'billings';

    protected $fillable = [
        'payment_id',
        'month',
        'year',
        'expires_at',
        'token',
        'value',
        'paid_value',
        'paid_at',
        'status',
        'discount_value',
        'extra_value',
        'amortization_value',
        'notes'
    ];

    public function payment() {
        return $this->belongsTo('App\Payment', 'payment_id');
    }

    public function billets() {
        return $this->morphMany('App\Billet', 'billetable');
    }

    // public function billets() {
    //     return $this->hasMany('App\Billet', 'billing_id');
    // }

    public function billet_generated() {
        return $this->billets->whereIn('status', [ 'SALVO', 'EMITIDO', 'REGISTRADO', 'LIQUIDADO', 'BAIXADO' ])->first();
    }

    public function statuses() {
        return $this->hasMany('App\BillingHistoryStatus', 'billing_id');
    }

    public function setStatus($status) {
        $this->update([ 'status' => $status ]);

        if($this->statuses->count() && $this->statuses->sortByDesc('created_at')->first()->status == $status) return;

        \App\BillingHistoryStatus::create([ 'billing_id' => $this->id, 'status' => $status ]);
    }

    public function getPaymentDate() {
        if($this->paid_at) return @dateTimeStringBR($this->paid_at);
        if($this->billet_generated() && $this->billet_generated()->PagamentoData) return @dateTimeStringBR(dateTimeStringInverse($this->billet_generated()->PagamentoData));

        $aheads = \App\Ahead::where('status', 'PAID')->get();
        if($aheads->count()) {
            foreach ($aheads as $key => $ahead) {
                if(in_array($this->id, json_decode($ahead->billings))) {
                    return $ahead->billet_generated()->PagamentoData;
                }
            }
        }

        return null;
    }
}
