<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model{

    use SoftDeletes;

    protected $table = 'addresses';

    protected $fillable = [
        'is_billing', 'zipcode', 'street', 'number', 'complement', 'district', 'city', 'state'
    ];
}
