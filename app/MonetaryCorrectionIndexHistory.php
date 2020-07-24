<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonetaryCorrectionIndexHistory extends Model {

    use SoftDeletes;

    protected $table = 'monetary_correction_indexes_history';

    protected $fillable = [
        'indexes_id',
        'value',
        'month',
        'year',
        'valid_at'
    ];

}
