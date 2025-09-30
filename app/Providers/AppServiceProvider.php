<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\UserCredential;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $isAdmin = false;

            if (auth()->check()) {
                $cred = UserCredential::where('matricula', auth()->user()->CODFUN)->first();
                $isAdmin = $cred && $cred->is_admin;
            }

            $view->with('isAdmin', $isAdmin);
        });
    }
}
