<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'category_id', 'images', 'currency', 'quantity_available', 'review'];
    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function cart()
    {
        return $this->hasMany('App\Models\cart', 'product_id');
    }

    protected $casts = [
        'images' => 'array',
    ];
}
