<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\TimeUtilities;

use App\User;

class Issue extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * Get the date string
     *
     * @return string
     */
    public function getDateAttribute()
    {
        return TimeUtilities::timestampToDateString($this->timestamp);
    }

    /**
     * Get the time string
     *
     * @return string
     */
    public function getTimeAttribute()
    {
        return TimeUtilities::timestampToTimeString($this->timestamp);
    }

    /**
     * Get the user name
     *
     * @return string
     */
    public function getCreatorNameAttribute()
    {
        return User::find($this->creator_id)->name;
    }

}
