<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model{

    use SoftDeletes;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = 'projects';

    protected $fillable = [
        'social_name',
        'cnpj',
        'name',
        'finish_at',
        'status',
        'local',
        'photo',
        'contract',
        'type',
        'constructor_id',
        'notes',
        'expiration_time',
        'comission',
        'fee',
        'minimum_percentage',
        'simulator',
        'indexes',
        'background_image',
        'chat',
        'url',
        'photo2',
        'chat_code',
        'map',
        'map_stages_position',
        'send_billets',
        'fields'
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'user_projects', 'project_id', 'user_id')->whereNull('user_projects.deleted_at')->withTimestamps();
    }

    public function companies() {
        return $this->belongsToMany('App\Company', 'company_projects', 'project_id', 'company_id')->whereNull('company_projects.deleted_at')->withTimestamps()->withPivot('id', 'file', 'email_sent');
    }

    public function buildings() {
        return $this->hasMany('App\Building', 'project_id');
    }

    public function user_projects() {
        return $this->hasMany('App\UserProject', 'project_id');
    }

    public function contracts() {
        return $this->hasMany('App\Contract', 'project_id');
    }

    public function leads() {
        return $this->hasMany('App\Lead', 'project_id');
    }

    public function constructor() {
        return $this->belongsTo('App\Constructor', 'constructor_id');
    }

    public function documents() {
        return $this->hasMany('App\ProjectDocument', 'project_id');
    }

    public function maps() {
        return $this->hasMany('App\Map', 'project_id');
    }

    public function properties() {
        return $this->hasManyDeep('App\Property', ['App\Building', 'App\Block'], ['project_id', 'building_id', 'block_id'], ['id', 'id', 'id']);
    }

    public function proposals_actives() {
        return $this->hasManyDeep('App\Proposal', ['App\Building', 'App\Block', 'App\Property'], ['project_id', 'building_id', 'block_id', 'property_id'], ['id', 'id', 'id', 'id'])->whereNotIn('status', ['REFUSED', 'CANCELED']);
    }

    public function proposals_sold() {
        return $this->hasManyDeep('App\Proposal', ['App\Building', 'App\Block', 'App\Property'], ['project_id', 'building_id', 'block_id', 'property_id'], ['id', 'id', 'id', 'id'])->where('status', 'SOLD');
    }

    public function proposals_review() {
        return $this->hasManyDeep('App\Proposal', ['App\Building', 'App\Block', 'App\Property'], ['project_id', 'building_id', 'block_id', 'property_id'], ['id', 'id', 'id', 'id'])->whereIn('status', ['RESERVED', 'PROPOSAL', 'PROPOSAL_REVIEW', 'DOCUMENTS_REVIEW', 'CONTRACT_ISSUE', 'CONTRACT_AVAILABLE', 'IN_SIGNATURE', 'QUEUE_1', 'QUEUE_2']);
    }

    public function getPropertiesAvailable() {
        return $this->properties->count() - $this->proposals_actives->groupBy('property_id')->count();
    }

    public function stages() {
        return $this->hasMany('App\ProjectStage', 'project_id');
    }

    public function owners() {
        return $this->belongsToMany('App\Owner', 'project_owners', 'project_id', 'owner_id')->whereNull('project_owners.deleted_at')->withTimestamps();
    }

    public function accounts() {
        return $this->hasMany('App\ProjectOwner', 'project_id');
    }

    public function all_accounts() {
        return $this->belongsToMany('App\Account', 'project_owners', 'project_id', 'account_id')->whereNull('project_owners.deleted_at')->withTimestamps();
    }
}
