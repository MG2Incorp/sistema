<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model{

    use SoftDeletes;

    protected $table = 'properties';

    protected $fillable = ['block_id', 'number', 'value', 'notes', 'size', 'dimensions', 'situation', 'owner', 'account_id', 'numero_matricula', 'cadastro_imobiliario'];

    public function block(){
        return $this->belongsTo('App\Block', 'block_id');
    }

    public function proposals(){
        return $this->hasMany('App\Proposal', 'property_id');
    }

    public function proposals_actives(){
        return $this->hasMany('App\Proposal', 'property_id')->whereNotIn('status', ['REFUSED', 'CANCELED'])->orderBy('created_at', 'ASC');
    }

    public function proposal_sold(){
        return $this->hasOne('App\Proposal', 'property_id')->where('status', 'SOLD');
    }

    public function map(){
        return $this->hasOne('App\Map', 'property_id');
    }

    public function account(){
        return $this->belongsTo('App\Account', 'account_id');
    }
}
