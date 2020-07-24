<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingHistoryStatus extends Model {

    use SoftDeletes;

    protected $table = 'billing_history_statuses';

    protected $fillable = [
        'billing_id',
        'status'
    ];

    public function billing() {
        return $this->belongsTo('App\Billing', 'billing_id');
    }
}
