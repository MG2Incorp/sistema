<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model{

    use SoftDeletes;

    protected $table = 'leads';

    protected $fillable = ['project_id', 'name', 'email', 'cellphone'];

    public function project(){
        return $this->belongsTo('App\Project', 'project_id');
    }
}
