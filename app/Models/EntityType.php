<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    use HasFactory;

    const Admin = 1;
    const User = 2;

    public $timestamps = false;
    protected $table = 'entity_type';
    protected $fillable = ['name'];
}
