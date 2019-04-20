<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'search', 'is_disabled', 'is_admin'
    ];

    /**
     * The events this user owns.
     */
    public function ownedEvents() {
      return $this->hasMany('App\Event');
    }

    /**
     * The events this user is attending.
     */
    public function attendingEvents() {
        // TODO: Add ->withPivot(columns...);
        return $this->belongsToMany('App\Event', 'tickets', 'event_id', 'user_id');
    }

    /**
     * The events this user is organizing.
     */
    public function organizingEvents() {
        return $this->belongsToMany('App\Event', 'organizers', 'event_id', 'user_id');
    }

    /**
     * The notifications of this user.
     */
    public function notifications() {
        return $this->hasMany('App\Notification', 'user_id', 'id');
    }
}
