<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      'App\Notification' => 'App\Policies\NotificationPolicy',
      'App\Event' => 'App\Policies\EventPolicy',
      'App\Issue' => 'App\Policies\IssuePolicy',
      'App\Rating' => 'App\Policies\RatingPolicy',
      'App\User' => 'App\Policies\UserPolicy',
      'App\Comment' => 'App\Policies\CommentPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
