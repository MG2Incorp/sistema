<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ahead extends Model {

    use SoftDeletes;

    protected $table = 'aheads';

    protected $fillable = [
        'payment_id',
        'token',
        'value',
        'paid_value',
        'status',
        'billings',
        'is_total'
    ];

    public function billets() {
        return $this->morphMany('App\Billet', 'billetable');
    }

    public function billet_generated() {
        return $this->billets->whereIn('status', [ 'SALVO', 'EMITIDO', 'REGISTRADO', 'LIQUIDADO', 'BAIXADO' ])->first();
    }

    public function payment() {
        return $this->belongsTo('App\Payment', 'payment_id');
    }

    public function statuses() {
        return $this->hasMany('App\AheadHistoryStatus', 'ahead_id');
    }

    public function setStatus($status) {
        $this->update([ 'status' => $status ]);

        if($this->statuses->count() && $this->statuses->sortByDesc('created_at')->first()->status == $status) return;

        \App\AheadHistoryStatus::create([ 'ahead_id' => $this->id, 'status' => $status ]);
    }
}
