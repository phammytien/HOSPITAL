<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = DB::table('system_settings')
            ->where('key', 'maintenance_mode')
            ->first();

        // If maintenance mode is enabled
        if ($maintenanceMode && $maintenanceMode->value == '1') {
            // Allow admin users to bypass maintenance mode
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            // Get maintenance message
            $maintenanceMessage = DB::table('system_settings')
                ->where('key', 'maintenance_message')
                ->first();

            $message = $maintenanceMessage ? $maintenanceMessage->value : 'Hệ thống đang bảo trì. Vui lòng quay lại sau.';

            // Logout non-admin users
            if (auth()->check()) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Return maintenance page
            return response()->view('maintenance', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
