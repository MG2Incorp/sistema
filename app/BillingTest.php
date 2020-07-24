<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingTest extends Model {

    use SoftDeletes;

    protected $table = 'billing_tests';

    protected $fillable = [
        'account_id',
        'value',
        'quantity',
        'TituloDocEspecie',
        'TituloAceite',
        'TituloLocalPagamento',
        'TituloCodEmissaoBloqueto'
    ];

    public function account() {
        return $this->belongsTo('App\Account', 'account_id');
    }

    public function billets() {
        return $this->morphMany('App\Billet', 'billetable');
    }
}
