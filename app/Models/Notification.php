<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'target_role',
        'is_read',
        'created_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get formatted notification time
     * 
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return \App\Helpers\TimeHelper::formatNotificationTime($this->created_at);
    }

    /**
     * Get formatted notification time with hour
     * 
     * @return string
     */
    public function getFormattedTimeWithHourAttribute()
    {
        return \App\Helpers\TimeHelper::formatNotificationTimeWithHour($this->created_at);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
