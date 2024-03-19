<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPdf extends Model
{
    protected $table = 'customer_pdf';
    protected $fillable = ['id', 'customer_id'];

    public $timestamps = false;

}
