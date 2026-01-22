<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
        
        // Apply maintenance mode check and audit logging to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Automatic database backup - runs every 30 seconds for testing
        // To change the interval, modify the system_settings table or use the admin panel
        $schedule->command('backup:database')->everyThirtySeconds();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

