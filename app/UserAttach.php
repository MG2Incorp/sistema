<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAttach extends Model{

    use SoftDeletes;

    protected $table = 'user_attachs';

    protected $fillable = ['user_id', 'attach_id', 'project_id'];
}
