<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalHistoryStatus extends Model{

    use SoftDeletes;

    protected $table = 'proposal_history_statuses';

    protected $fillable = ['proposal_id', 'status', 'user_id', 'notes'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
