<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function relation_user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function relation_transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function relation_service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
