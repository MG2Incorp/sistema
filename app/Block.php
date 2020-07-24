<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model{

    use SoftDeletes;

    protected $table = 'blocks';

    protected $fillable = ['label', 'building_id'];

    public function building(){
        return $this->belongsTo('App\Building', 'building_id');
    }

    public function properties(){
        return $this->hasMany('App\Property', 'block_id');
    }
}
