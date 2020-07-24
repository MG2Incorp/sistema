<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BilletError extends Model {

    use SoftDeletes;

    protected $table = 'billet_errors';

    protected $fillable = [
        'billet_id',
        'message'
    ];

    public function billet() {
        return $this->belongsTo('App\Billet', 'billet_id');
    }
}
