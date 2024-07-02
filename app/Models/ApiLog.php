<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'url_link',
        'ip_address',
        'status_response',
    ];
}
