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
        return $this->belongsToMany('App\Event', 'tickets', 'user_id', 'event_id');
    }

    /**
     * The events this user is organizing.
     */
    public function organizingEvents() {
        return $this->belongsToMany('App\Event', 'organizers', 'user_id', 'event_id');
    }

    /**
     * The events this user has marked as favorite.
     */
    public function favoriteEvents() {
        return $this->belongsToMany('App\Event', 'favorites', 'user_id', 'event_id');
    }

    /**
     * The notifications of this user.
     */
    public function notifications() {
        return $this->hasMany('App\Notification', 'user_id', 'id');
    }

    public function scopeFTS($query, $search) {

        return $query->selectRaw('id, name, email, is_admin, is_disabled')
        ->whereRaw("search @@ plainto_tsquery('english', ?)", [$search])
        ->orderByRaw("ts_rank(search, plainto_tsquery('english', ?)) DESC", [$search]);
    }
}
