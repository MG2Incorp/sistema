<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model {

    use SoftDeletes;

    protected $table = 'owners';

    protected $fillable = [
        'alias',
        'social_name',
        'name',
        'document',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'cidade',
        'cidade_ibge',
        'uf',
        'telefone',
        'email',
        'status',
        'plugboleto_id'
    ];

    public function accounts() {
        return $this->hasMany('App\Account', 'owner_id');
    }
}
