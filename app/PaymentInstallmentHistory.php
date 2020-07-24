<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentInstallmentHistory extends Model {

    use SoftDeletes;

    protected $table = 'payments_installments_history';

    protected $fillable = [ 'payment_id', 'month', 'year', 'value' ];
}
