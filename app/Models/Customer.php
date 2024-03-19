<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;



class Customer extends Model
{
    use SoftDeletes;

    const SAVE = 1;
    const SAVE_AND_MOVE = 2;
    protected $table = 'customers';
    protected $fillable = [
        'id',
        'uuid',
        'client_code',
    ];

    public function customerDetail()
    {
        return $this->hasOne(CustomerDetail::class);
    }
    public function customerPdf()
    {
        return $this->hasOne(CustomerPdf::class);
    }
}
