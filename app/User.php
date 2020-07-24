<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role', 'cpf', 'name', 'email', 'password', 'company_id', 'phone', 'creci', 'constructor_id', 'receive_emails', 'created_by'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function projects() {
        return $this->belongsToMany('App\Project', 'user_projects', 'user_id', 'project_id')->whereNull('user_projects.deleted_at')->withTimestamps();
    }

    public function permissions() {
        return $this->hasManyThrough('App\UserPermission', 'App\UserProject', 'user_id', 'user_project_id', 'id', 'id');
    }

    public function user_permissions() {
        return $this->hasMany('App\UserPermission', 'user_id');
    }

    // public function company(){
    //     return $this->belongsTo('App\Company', 'company_id');
    // }

    public function constructor(){
        return $this->belongsTo('App\Constructor', 'constructor_id');
    }

    public function user_projects() {
        return $this->hasMany('App\UserProject', 'user_id');
    }

    public function user_projects_with_trashed() {
        return $this->hasMany('App\UserProject', 'user_id')->withTrashed();
    }

    public function companies(){
        return $this->belongsToMany('App\Company', 'user_companies', 'user_id', 'company_id')->whereNull('user_companies.deleted_at')->withTimestamps();
    }

    public function user_companies(){
        return $this->hasMany('App\UserCompany', 'user_id');
    }

    public function messages_sent(){
        return $this->hasMany('App\Message', 'sender');
    }

    public function messages_received(){
        return $this->hasMany('App\Message', 'receiver');
    }

    public function users_created() {
        return $this->hasMany('App\User', 'created_by');
    }

    public function users(){
        return $this->belongsToMany('App\User', 'user_attachs', 'user_id', 'attach_id')->whereNull('user_attachs.deleted_at')->withTimestamps();

        // return $this->hasMany('App\User', 'created_by');
    }

    public function attachs(){
        return $this->hasMany('App\UserAttach', 'attach_id');
    }

    public function messages_not_read(){
        return $this->hasMany('App\Message', 'receiver')->where('read_at', null);
    }

    public function getPermissions($project_id) {
        $user_project = UserProject::where('user_id', $this->id)->where('project_id', $project_id)->first();
        if ($user_project) {
            $permissions = UserPermission::where('user_project_id', $user_project->id)->get();
            if ($permissions) {
                return $permissions;
            }
        }

        return collect();
    }

    public function checkPermission($project_id, $permissions = array()) {
        return $this->getPermissions($project_id)->contains(function ($value, $key) use ($permissions) { return in_array($value->permission, $permissions); }) && !empty(array_intersect($permissions, getPermissions($this->role)));
    }

    public function checkPermissionOrAdmin($project_id, $permissions = array()) {
        return $this->role == 'ADMIN' || ($this->getPermissions($project_id)->contains(function ($value, $key) use ($permissions) { return in_array($value->permission, $permissions); }) && !empty(array_intersect($permissions, getPermissions($this->role))));
    }

    public function getPermissionsNew($project_id, $company_id) {
        $user_project = UserProject::where('user_id', $this->id)->where('project_id', $project_id)->where('company_id', $company_id)->first();
        if ($user_project) {
            $permissions = UserPermission::where('user_project_id', $user_project->id)->get();
            if ($permissions) {
                return $permissions;
            }
        }

        return collect();
    }

    public function checkPermissionNew($project_id, $company_id, $permissions = array()) {
        return $this->getPermissionsNew($project_id, $company_id)->contains(function ($value, $key) use ($permissions) { return in_array($value->permission, $permissions); }) && !empty(array_intersect($permissions, getPermissions($this->role)));
    }

    public function hasUserPermission($permissions = array()) {
        return $this->user_permissions->contains(function ($value, $key) use ($permissions) { return in_array($value->permission, $permissions); });
    }
}
