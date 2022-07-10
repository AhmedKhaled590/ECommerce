<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as PasswordsCanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, PasswordsCanResetPassword, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'state',
        'city',
        'email_verified_at',
        'avatar',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasOne('App\Models\cart');
    }

    protected function firstName(): Attribute
    {
        $splits = explode(' ', $this->name);
        $toReturn = 0;
        //check if first name is prof or mr or mrs or miss or dr or sir or rev or etc and return the first name
        if ($splits[0] == 'Prof.' || $splits[0] == 'Mr.' || $splits[0] == 'Mrs.' || $splits[0] == 'Miss.' || $splits[0] == 'Dr.' || $splits[0] == 'Sir' || $splits[0] == 'Rev') {
            $toReturn = 1;
        }
        return Attribute::make(
            get:fn($value) => explode(' ', $this->name)[$toReturn],
        );
    }

    protected function lastName(): Attribute
    {
        $splits = explode(' ', $this->name);
        return Attribute::make(
            get:fn($value) => explode(' ', $this->name)[count($splits) - 1],
        );
    }

    public static function last()
    {
        return static::all()->last();
    }
}
