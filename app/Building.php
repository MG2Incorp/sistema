<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model{

    use SoftDeletes;

    protected $table = 'buildings';

    protected $fillable = ['project_id', 'name'];

    public function project(){
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function blocks() {
        return $this->hasMany('App\Block', 'building_id');
    }

}
