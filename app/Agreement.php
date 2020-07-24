<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model {

    use SoftDeletes;

    protected $table = 'agreements';

    protected $fillable = [
        'account_id',
        'numero',
        'descricao',
        'carteira',
        'especie',
        'cnab',
        'reiniciar',
        'numero_remessa',
        'utiliza_van',
        'densidade_remessa',
        'nosso_numero_banco',
        'plugboleto_id'
    ];

    public function account() {
        return $this->belongsTo('App\Account', 'account_id');
    }
}
