<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Tweet;
use App\Policies\TweetPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Tweet::class => TweetPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url') . '/password-reset?token=' . $token . '&email=' . $user->getEmailForPasswordReset();
        });
    }
}
