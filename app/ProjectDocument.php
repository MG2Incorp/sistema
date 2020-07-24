<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDocument extends Model{

    use SoftDeletes;

    protected $table = 'project_documents';

    protected $fillable = ['user_id', 'project_id', 'file', 'description'];

    public function project(){
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
