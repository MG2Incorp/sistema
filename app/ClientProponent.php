<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientProponent extends Model
{
    use SoftDeletes;

    protected $table = 'client_proponents';

    protected $fillable = [
        'client_id', 'proponent_id',
    ];
}