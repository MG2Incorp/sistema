<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProject extends Model
{
    use SoftDeletes;

    protected $table = 'user_projects';

    protected $fillable = ['code', 'user_id', 'project_id', 'company_id', 'file', 'email_sent', 'situation'];

    public function permissions() {
        return $this->hasMany('App\UserPermissions', 'user_project_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function project() {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function company() {
        return $this->belongsTo('App\Company', 'company_id');
    }
}
