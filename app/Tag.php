<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $fillable = ['id', 'name'];

    public $incrementing = false;

    /**
     * The events that have this tag.
     */
    public function events() {
        return $this->belongsToMany('App\Event', 'event_tags', 'tag_id', 'event_id');
    }
}
