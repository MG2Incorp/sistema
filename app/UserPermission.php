<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermission extends Model
{
    use SoftDeletes;

    protected $table = 'user_permissions';

    protected $fillable = ['user_id', 'user_project_id', 'permission'];
}