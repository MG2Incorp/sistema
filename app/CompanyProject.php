<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyProject extends Model
{
    use SoftDeletes;

    protected $table = 'company_projects';

    protected $fillable = ['code', 'company_id', 'project_id', 'file', 'email_sent', 'situation'];

    public function project() {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function company() {
        return $this->belongsTo('App\Company', 'company_id');
    }
}
