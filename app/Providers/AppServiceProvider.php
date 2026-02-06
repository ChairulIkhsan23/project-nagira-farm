<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

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
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        /**
         * Custom reset password email
         */
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new MailMessage)
                ->subject(__('email.reset_subject'))      
                ->greeting(__('email.greeting'))          
                ->line(__('email.sent'))                  
                ->line(__('email.notice'))                
                ->action(__('email.reset_action'), url(route('password.reset', $token, false))) // Tombol
                ->salutation(__('email.salutation'));
        });
    }
}
