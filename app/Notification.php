<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // TODO: Discuss
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The user that owns these notifications.
     */
    public function ownedEvents() {
        return $this->belongsTo('App\User');
    }
}
