<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model{

    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = ['name', 'creci', 'cnpj', 'manager', 'email', 'telephone', 'cellphone', 'address_id'];

    public function address() {
        return $this->belongsTo('App\Address', 'address_id');
    }

    public function projects() {
        return $this->belongsToMany('App\Project', 'company_projects', 'company_id', 'project_id')->whereNull('company_projects.deleted_at')->withTimestamps();
    }

    public function agents(){
        return $this->belongsToMany('App\User', 'user_companies', 'company_id', 'user_id')->where('is_coordinator', 0)->whereNull('user_companies.deleted_at')->withTimestamps();
    }

    public function users(){
        return $this->belongsToMany('App\User', 'user_companies', 'company_id', 'user_id')->whereNull('user_companies.deleted_at')->withTimestamps();
    }

    public function coordinators(){
        return $this->belongsToMany('App\User', 'user_companies', 'company_id', 'user_id')->where('is_coordinator', 1)->whereNull('user_companies.deleted_at')->withTimestamps();
    }
}
