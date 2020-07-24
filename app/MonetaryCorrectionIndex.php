<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonetaryCorrectionIndex extends Model {

    use SoftDeletes;

    protected $table = 'monetary_correction_indexes';

    protected $fillable = [
        'name'
    ];

    public function history() {
        return $this->hasMany('App\MonetaryCorrectionIndexHistory', 'indexes_id');
    }

}
