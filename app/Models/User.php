<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','phone','role',
    ];

    protected $hidden   = ['password','remember_token'];
    protected $casts    = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Get all orders for this user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get all services through orders
     */
    public function services()
    {
        return $this->hasManyThrough(Service::class, Order::class);
    }
}
