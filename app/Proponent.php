<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proponent extends Model{

    use SoftDeletes;

    protected $table = 'proponents';

    protected $fillable = [
        'client_id', 'proposal_id', 'proponent_id', 'main', 'type', 'name', 'document', 'proportion', 'rg', 'emitter', 'rg_state', 'email', 'gender', 'birthdate', 'phone', 'cellphone', 'mother_name', 'father_name',
        'birthplace', 'country', 'house', 'gross_income', 'net_income', 'occupation', 'registry', 'civil_status', 'marriage', 'company', 'company_document', 'role', 'hired_at', 'company_phone',
        'company_cellphone', 'address_id', 'company_address_id'
    ];

    public function proposal() {
        return $this->belongsTo('App\Proposal', 'proposal_id');
    }

    public function proponent() {
        return $this->hasOne('App\Proponent', 'proponent_id');
    }

    // public function documents() {
    //     return $this->hasMany('App\Document', 'proponent_id');
    // }

    public function documents() {
        return $this->hasMany('App\Document', 'proponent_id')->where('type', '!=', 'other');
    }

    public function documents_attach() {
        return $this->hasMany('App\Document', 'proponent_id')->where('type', 'other');
    }

    public function address() {
        return $this->belongsTo('App\Address', 'address_id');
    }

    public function company_address() {
        return $this->belongsTo('App\Address', 'company_address_id');
    }
}
