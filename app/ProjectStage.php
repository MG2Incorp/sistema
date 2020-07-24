<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStage extends Model {

    use SoftDeletes;

    protected $table = 'project_stages';

    protected $fillable = [ 'project_id', 'stage_id', 'percentage', 'is_visible', 'start_at', 'show_start_at' ];

    public function project() {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function stage() {
        return $this->belongsTo('App\Stage', 'stage_id');
    }
}
