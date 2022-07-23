<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class transactions extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'transactions';
    protected $fillable = ['stripe_id', 'user_id', 'cart_id', 'amount', 'currency', 'payment_method_id', 'payment_method_type', 'status'];
}
