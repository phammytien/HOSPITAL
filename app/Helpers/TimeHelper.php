<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    /**
     * Format notification time
     * - If today: show relative time (e.g., "15 phút trước")
     * - If different day: show date (e.g., "12/1/2026")
     * 
     * @param Carbon|string $datetime
     * @return string
     */
    public static function formatNotificationTime($datetime)
    {
        if (!$datetime) {
            return '';
        }

        $date = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $now = Carbon::now();

        // Check if it's today
        if ($date->isToday()) {
            // Show relative time for today
            $diffInMinutes = (int) abs($now->diffInMinutes($date));
            $diffInHours = (int) abs($now->diffInHours($date));
            
            if ($diffInMinutes < 1) {
                return 'Vừa xong';
            } elseif ($diffInMinutes < 60) {
                return $diffInMinutes . ' phút trước';
            } else {
                return $diffInHours . ' giờ trước';
            }
        }
        
        // For different days, show date in format: d/m/Y
        return $date->format('d/m/Y');
    }

    /**
     * Format notification time with time included
     * - If today: show relative time (e.g., "15 phút trước")
     * - If different day: show date with time (e.g., "12/1/2026 14:30")
     * 
     * @param Carbon|string $datetime
     * @return string
     */
    public static function formatNotificationTimeWithHour($datetime)
    {
        if (!$datetime) {
            return '';
        }

        $date = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $now = Carbon::now();

        // Check if it's today
        if ($date->isToday()) {
            // Show relative time for today
            $diffInMinutes = (int) abs($now->diffInMinutes($date));
            $diffInHours = (int) abs($now->diffInHours($date));
            
            if ($diffInMinutes < 1) {
                return 'Vừa xong';
            } elseif ($diffInMinutes < 60) {
                return $diffInMinutes . ' phút trước';
            } else {
                return $diffInHours . ' giờ trước';
            }
        }
        
        // For different days, show date with time in format: d/m/Y H:i
        return $date->format('d/m/Y H:i');
    }
}
