<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostAllow extends Model
{
    use HasFactory;

    public $fillable = [
        'host_id',
        'allow_host_id'
    ];
}
