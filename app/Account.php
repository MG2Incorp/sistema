<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model {

    use SoftDeletes;

    protected $table = 'accounts';

    protected $fillable = [
        'owner_id',
        'bank_code',
        'agency',
        'agency_dv',
        'number',
        'number_dv',
        'type',
        'beneficiario',
        'company_code',
        'status',
        'inicio_nosso_numero',
        'plugboleto_id'
    ];

    public function properties() {
        return $this->hasMany('App\Property', 'account_id');
    }

    public function owner() {
        return $this->belongsTo('App\Owner', 'owner_id');
    }

    public function agreement() {
        return $this->hasOne('App\Agreement', 'account_id');
    }
}
