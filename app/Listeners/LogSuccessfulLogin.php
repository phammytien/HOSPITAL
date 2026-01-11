<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class LogSuccessfulLogin
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Đăng nhập thành công',
            'description' => 'Người dùng đăng nhập vào hệ thống',
            'ip_address' => $this->request->ip(),
            'device_agent' => $this->request->userAgent(),
        ]);
    }
}
