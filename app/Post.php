<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\TimeUtilities;

class Post extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The creator of this post.
     */
    public function creator() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * The event this post belongs to.
     */
    public function event() {
        return $this->belongsTo('App\Event', 'event_id');
    }

    /**
     * The comments this post has.
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'post_id', 'id');
    }

    /**
     * Get the start date string
     *
     * @return string
     */
    public function getFormattedTimestampAttribute()
    {
        return TimeUtilities::timestampToString($this->timestamp);
    }


    /**
     * Scope a query to only include Posts that are Announcements
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnouncements($query) {
        return $query->where('is_announcement', true);
    }

    /**
     * Scope a query to only include Posts that are Discussions
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiscussions($query) {
        return $query->where('is_announcement', false);
    }
}
