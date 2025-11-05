<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
    protected $fillable = ['order_id','gateway','direction','payload','status'];
    protected $casts = ['payload' => 'array'];

    public function order(){ return $this->belongsTo(Order::class); }
}
