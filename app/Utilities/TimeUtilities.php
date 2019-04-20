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

}

?>