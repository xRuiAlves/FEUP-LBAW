<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The events this category is assigned to.
     */
    public function events() {
        return $this->hasMany('App\Event', 'event_category_id', 'id');
    }

    /**
     * The number of events this category is assigned to.
     */
    public function getNEventsAttribute() {
        return $this->events->count();
    }
}
