<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventVoucher extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The user that consumed the voucher.
     */
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * The event the voucher is for.
     */
    public function event() {
        return $this->belongsTo('App\Event', 'event_id');
    }
}
