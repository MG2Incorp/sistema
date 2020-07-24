<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCompany extends Model
{
    use SoftDeletes;

    protected $table = 'user_companies';

    protected $fillable = ['user_id', 'company_id', 'is_coordinator'];

}
