<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogSuccessfulLogin::class
        );

        \Illuminate\Support\Facades\View::composer(['layouts.department', 'layouts.buyer', 'layouts.admin'], function ($view) {
            $notifications = [];
            $unreadCount = 0;

            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $role = strtoupper($user->role);

                $roleLower = strtolower($role);
                $baseQuery = \App\Models\Notification::where(function ($q) use ($role, $roleLower) {
                    $q->whereIn('target_role', [$role, $roleLower, 'ALL', 'all'])
                        ->orWhereNull('target_role');
                });

                $unreadCount = (clone $baseQuery)->where('is_read', false)->count();
                $notifications = $baseQuery->orderBy('created_at', 'desc')->take(10)->get();
            }

            $view->with('notifications', $notifications)
                ->with('unreadCount', $unreadCount);
        });
    }
}
