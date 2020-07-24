<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model{

    use SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = ['name', 'project_id', 'content'];
}
