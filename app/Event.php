<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\TimeUtilities;

class Event extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    // Investigate:
    // protected $dateFormat = '';

    /**
     * The user this Event belongs to.
     */
    public function owner() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * The attendees of this event.
     */
    public function attendees() {
        // TODO: Add ->withPivot(columns...);
        return $this->belongsToMany('App\User', 'tickets', 'user_id', 'event_id');
    }

    /**
     * The category of this event.
     */
    public function category() {
        return $this->belongsTo('App\EventCategory', 'event_category_id');
    }

    /**
     * The posts this event has.
     */
    public function posts() {
        return $this->hasMany('App\Post', 'event_id', 'id');
    }

    /**
     * Get the start date string
     *
     * @return string
     */
    public function getStartDateAttribute()
    {
        return TimeUtilities::timestampToDateString($this->start_timestamp);
    }

    /**
     * Get the end date string
     *
     * @return string
     */
    public function getEndDateAttribute()
    {
        return $this->end_timestamp ? TimeUtilities::timestampToDateString($this->end_timestamp) : null;
    }

    /**
     * Get the start time string
     *
     * @return string
     */
    public function getStartTimeAttribute()
    {
        return TimeUtilities::timestampToTimeString($this->start_timestamp);
    }

    /**
     * Get the end time string
     *
     * @return string
     */
    public function getEndTimeAttribute()
    {
        return $this->end_timestamp ? TimeUtilities::timestampToTimeString($this->end_timestamp) : null;
    }

    /**
     * Get the start timestamp string
     *
     * @return string
     */
    public function getFormattedStartTimestampAttribute()
    {
        return TimeUtilities::timestampToString($this->start_timestamp);
    }

    /**
     * Get the end timestamp string
     *
     * @return string
     */
    public function getFormattedEndTimestampAttribute()
    {
        return $this->end_timestamp ? TimeUtilities::timestampToString($this->end_timestamp) : null;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'search',
    ];

    /**
     * Scope a query to only include most relevant events
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelevant($query) {
        return $query->futureEvents()->orderBy('start_timestamp', 'asc');
    }

    /**
     * Scope a query to only include active events
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('status', 'Active');
    }

    /**
     * Scope a query to only include the active future events
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFutureEvents($query) {
        return $query->active()->where('start_timestamp', '>', 'NOW()');
    }

}
