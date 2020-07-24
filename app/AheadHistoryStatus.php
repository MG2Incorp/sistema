<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AheadHistoryStatus extends Model {

    use SoftDeletes;

    protected $table = 'ahead_history_statuses';

    protected $fillable = [
        'ahead_id',
        'status'
    ];

    public function ahead() {
        return $this->belongsTo('App\Ahead', 'ahead_id');
    }
}
