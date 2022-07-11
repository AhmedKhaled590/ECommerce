<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Password::default(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised();
            return $rule;
        });
        Log::shareContext([
            'correlation_id' =>  now()->toISOString()
        ]);
    }
}
