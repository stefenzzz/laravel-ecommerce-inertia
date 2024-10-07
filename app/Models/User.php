<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\AddressType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


/**
 * The User model represents a user in the system.
 *
 * This model implements the MustVerifyEmail interface for email verification
 * and extends the Authenticatable class for authentication capabilities.
 *
 * @extends \Illuminate\Foundation\Auth\User
 * @implements \Illuminate\Contracts\Auth\Authenticatable
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $appends = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Accessor to get the user's full name.
     *
     * @return string
    */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function cartItems(): HasMany
    {   
        return $this->hasMany(CartItem::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class,'created_by', 'id');
    }

    public function shippingAddress(): HasOne
    {
        return $this->HasOne(Address::class,'user_id','id')->where('type', AddressType::Shipping);
    }

    public function billingAddress(): HasOne
    {
        return $this->HasOne(Address::class,'user_id','id')->where('type', AddressType::Billing);
    }
    
    public function hasRole($roles):bool
    {
        return in_array($this->role->code, $roles);
    }

}
