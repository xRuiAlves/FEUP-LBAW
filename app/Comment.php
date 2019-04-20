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
    public function creator() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * The post this Comment belongs to.
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }
}
