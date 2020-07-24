<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model{

    use SoftDeletes;

    protected $table = 'documents';

    protected $fillable = ['user_id', 'proposal_id', 'proponent_id', 'file', 'type', 'text'];

    public function proposal(){
        return $this->belongsTo('App\Proposal', 'proposal_id');
    }

    public function proponent(){
        return $this->belongsTo('App\Proponent', 'proponent_id');
    }
}
