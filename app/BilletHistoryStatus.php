<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BilletHistoryStatus extends Model {

    use SoftDeletes;

    protected $table = 'billet_history_statuses';

    protected $fillable = [
        'billet_id',
        'status'
    ];

    public function billet() {
        return $this->belongsTo('App\Billet', 'billet_id');
    }
}
