<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // TODO: Discuss this
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The user this Comment belongs to.
     */
    public function owner() {
        return $this->belongsTo('App\User');
    }

    /**
     * The post this Comment belongs to.
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }
}
