<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Constructor extends Model {

    use SoftDeletes;

    protected $table = 'constructors';

    protected $fillable = ['name', 'cnpj'];

    public function projects() {
        return $this->hasMany('App\Project', 'constructor_id');
    }

    public function users() {
        return $this->hasMany('App\User', 'constructor_id');
    }
}
