<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'status', 'amount', 'type', 'session_id', 'created_by', 'updated_by'];

    protected $casts = [
        'status' => PaymentStatus::class
    ];
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id', 'id');
    }
}
