<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [ 'total_price', 'created_by', 'updated_by'];

    protected $appends = ['is_paid', 'status'];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class,'order_id', 'id');
    }

    public function getIsPaidAttribute(): bool // getIsPaidAttribute will be is_paid attribute set in $appends
    {
        return $this->payment->status === PaymentStatus::Paid;
    }
    public function getStatusAttribute(): PaymentStatus // getStatusAttribute will be status attribute set in $appends
    {
        return $this->payment->status;
    }

}
