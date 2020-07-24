<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model{

    use SoftDeletes;

    protected $table = 'messages';

    protected $fillable = ['sender', 'receiver', 'message', 'read_at'];

    public function from(){
        return $this->belongsTo('App\User', 'sender');
    }

    public function to(){
        return $this->belongsTo('App\User', 'receiver');
    }
}
