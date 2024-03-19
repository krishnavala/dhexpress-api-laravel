<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomerDetail extends Model
{
    use SoftDeletes;

    protected $table = 'customer_detail';

    protected $fillable = [
        'id',
        'customer_id',
        'group',
        'customer_name',
        'address',
        'pin_code',
        'contact_no',
        'invoice',
        'remarks',
    ];

}
