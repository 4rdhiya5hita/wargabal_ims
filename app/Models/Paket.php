<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function relation_user()
    {
        return $this->belongsTo(User::class);
    }

    public function relation_akses()
    {
        return $this->belongsTo(Akses::class, 'id_akses', 'id');
    }
}
