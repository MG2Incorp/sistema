<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Map extends Model {

    use SoftDeletes;

    protected $table = 'maps';

    protected $fillable = [
        'project_id',
        'property_id',
        'coordinates',
        'shape',
        'title',
    ];

    public function property() {
        return $this->belongsTo('App\Property', 'property_id');
    }
}
