<?php

namespace App\Utilities;

class TimeUtilities {

    public static function timestampToDateString($timestamp){
        return date('d-m-Y', strtotime($timestamp));
    }

    public static function timestampToTimeString($timestamp){
        return date('H:i', strtotime($timestamp));
    }

    public static function timestampToString($timestamp){
        return date('d-m-Y, H:i', strtotime($timestamp));
    }

    public static function timestampToYear($timestamp) {
        return date('Y', strtotime($timestamp));
    }

    public static function timestampToMonthShort($timestamp) {
        return date('M', strtotime($timestamp));
    }

    public static function timestampToDay($timestamp) {
        return date('d', strtotime($timestamp));
    }

    public static function timestampToDayOfWeek($timestamp) {
        return date('D', strtotime($timestamp));
    }
}