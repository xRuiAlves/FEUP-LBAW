<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Post;
use App\Event;
use Illuminate\Support\Facades\DB;

class Rating extends Model
{
    protected $fillable = ['value'];
    protected $primaryKey = 'post_id';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The user this Rating belongs to.
     */
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * The post this Rating belongs to.
     */
    public function post() {
        return $this->belongsTo('App\Post', 'post_id');
    }

    /**
     * Rating votes created by the user
     */
    public function scopeOwner($query, $owner) {
        return $query->where('user_id', $owner);
    }

    /**
     * Rating votes on a given post
     */
    public function scopeParent($query, $parent) {
        return $query->where('post_id', $parent);
    }
}
