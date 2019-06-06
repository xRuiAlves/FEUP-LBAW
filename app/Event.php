<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\TimeUtilities;

class Event extends Model
{

    // protected $appends = ['href'];


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
        return $this->belongsToMany('App\User', 'tickets', 'event_id', 'user_id');
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
     * The users that marked this event as favorite.
     */
    public function usersFavorited() {
        return $this->belongsToMany('App\User', 'favorites', 'event_id', 'user_id');        
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
     * Get the start date year
     *
     * @return string
     */
    public function getStartDateYearAttribute()
    {
        return TimeUtilities::timestampToYear($this->start_timestamp);
    }

    /**
     * Get the start date month
     *
     * @return string
     */
    public function getStartDateMonthAttribute()
    {
        return TimeUtilities::timestampToMonthShort($this->start_timestamp);
    }

    /**
     * Get the start date day
     *
     * @return string
     */
    public function getStartDateDayAttribute()
    {
        return TimeUtilities::timestampToDay($this->start_timestamp);
    }

    /**
     * Get the start date day as a day of the week
     *
     * @return string
     */
    public function getStartDateDayOfWeekAttribute()
    {
        return TimeUtilities::timestampToDayOfWeek($this->start_timestamp);
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
     * Get the link to event page
     *
     * @return string
     */
    public function getHrefAttribute()
    {
        return "/event/" . $this->id;
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_disabled',
    ];

    /**
     * Scope a query to include events' categories
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCategory($query) {
        return $query
        ->select()
        ->addSelect(DB::raw('events.id as id, event_categories.name AS category'))
        ->join('event_categories', 'events.event_category_id', '=', 'event_categories.id');
    }

    /**
     * Scope a query to only include most relevant events
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelevant($query) {
        return $query
        ->futureEvents()
        ->select()
        ->addSelect(DB::raw('events.id as id, event_categories.name AS category'))
        ->join('event_categories', 'events.event_category_id', '=', 'event_categories.id')
        ->orderBy('start_timestamp', 'asc');
    }

    /**
     * Scope a query to only include active events
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_disabled', false)->where('is_cancelled', false);
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



    protected function LocationScope($query, $location) {
        return $query->where('location', 'ilike', '%'.$location.'%');
    }


    /**
     * Scope a query to only include events located at given parameter
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocated($query, $location) {
        $this->LocationScope($query, $location);
    }

    
    protected function FTSScope($query, $search) {
        return $query->selectRaw('events.id, title, price, latitude, longitude, start_timestamp, end_timestamp, event_categories.name AS category')
        ->whereRaw("search @@ plainto_tsquery('english', ?)", [$search])
        ->orderByRaw("ts_rank(search, plainto_tsquery('english', ?)) DESC", [$search]);

    }

    public function scopeFTS($query, $search) {

        $this->FTSScope($query, $search);
    }

    protected function CategoryScope($query, $category_id) {
        return $query->where('event_category_id', '=', $category_id);

    }

    public function scopeCategory($query, $category_id) {
        $this->CategoryScope($query, $category_id);
    }


    protected function StartScope($query, $start_date) {
        return $query->where('start_timestamp', '>=', $start_date);

    }

    public function scopeStart($query, $start_date) {
        $this->StartScope($query, $start_date);
    }

    protected function EndScope($query, $end_date) {
        return $query->where('start_timestamp', '<=', $end_date);

    }

    public function scopeEnd($query, $end_date) {
        $this->EndScope($query, $end_date);
    }

    public function scopeFavorited($query, $event_id, $user_id) {
        return !$query
        ->join('favorites', 'events.id', '=', 'favorites.event_id')
        ->whereRaw('favorites.user_id = ? AND favorites.event_id = ?', [$user_id, $event_id])
        ->get()
        ->isEmpty();
    }

}
