<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    // Investigate:
    // protected $dateFormat = '';

    /**
     * The user this Event belongs to
     */
    public function owner() {
        return $this->belongsTo('App\User');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'search',
    ];
}
