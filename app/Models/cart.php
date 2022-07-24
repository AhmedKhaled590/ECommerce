<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'product_id', 'quantity'];
    // protected $attributes = [
    //     'price_per_quantity' => 0,
    // ];

    public function products()
    {
        return $this->belongsTo('App\Models\product', 'product_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    protected function pricePerQuantity(): Attribute
    {
        return new Attribute(
            get:fn($value) => $value * 2,
        );

    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->user_id = auth()->user() ? auth()->user()->id : $model->user_id;
            $model->price_per_quantity = $model->products->price * $model->quantity / 2;
            if ($model->quantity > $model->products->quantity_available) {
                return false;
            }
            $model->products->save();
        });

        static::deleted(function ($model) {
            $model->products->quantity_available += $model->quantity;
            $model->products->save();
        });
    }
}
