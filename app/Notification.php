<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\TimeUtilities;

class Notification extends Model
{
    // TODO: Discuss
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    static $typeTitles = 
        ['IssueNotification' => 'An issue you created has been solved',
        'EventInvitation' => 'Event Invitation',
        'EventDisabling' => 'An Event in your dashboard was disabled',
        'EventActivation' => 'A previously disabled event in your dashboard was re-enabled',
        'EventCancellation' => 'An Event in your dashboard was cancelled',
        'EventRemoval' => 'You have been removed from an Event',
        'EventOrganizer' => 'You have become an organizer of an event',
        'EventUpdate' => 'Event Update',
        'EventAnnouncement' => 'Event Announcement'
        ];

    /**
     * The user that owns these notifications.
     */
    public function ownedEvents() {
        return $this->belongsTo('App\User');
    }

    /**
     * The issue in case of an issue notification
     */
    public function issue(){
        return $this->belongsTo('App\Issue');
    }

    /**
     * The event in case of an event notification
     */
    public function event(){
        return $this->belongsTo('App\Event');
    }

    /**
     * Get the timestamp string
     *
     * @return string
     */
    public function getFormattedTimestampAttribute()
    {
        return TimeUtilities::timestampToString($this->timestamp);
    }

    /**
     * Scope a query to only include notifications not dismissed
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotSeen($query) {
        return $query->where('is_dismissed', false);
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if(!empty(Notification::$typeTitles[$this->type])){
            return Notification::$typeTitles[$this->type];
        }
    }

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessageAttribute()
    {
        switch($this->type){
            case('IssueNotification'):
                return 'Issue "' . $this->issue->title . '" has been solved: ' . $this->content;
            case('EventInvitation'):
                return 'You have been invited to join "' . $this->event->title . '"';
            case('EventDisabling'):
                return 'The event "' . $this->event->title . '" has been disabled';
            case('EventActivation'):
                return 'The event "' . $this->event->title . '" has been re-enabled';
            case('EventCancellation'):
                return 'The event "' . $this->event->title . '" has been cancelled';
            case('EventRemoval'):
                return 'You have been removed from "' . $this->event->title . '"';
            case('EventOrganizer'):
                return 'You are now an organizer of "' . $this->event->title . '"';
            case('EventUpdate'):
                return 'The details of "' . $this->event->title . '" have been updated. Make sure to check them as soon as possible';
            case('EventAnnouncement'):
                return 'There is a new announcement on "' . $this->event->title . '". Make sure to go to its page and keep up with the news';
        }
    }

    /**
     * Get the href
     *
     * @return string
     */
    public function getHrefAttribute()
    {
        if($this->type === 'IssueNotification'){
            return '#';
        }else{
            return $this->event->href;
        }
    }

    public function scopeInvitedEvents($query){
        return $query->where('type', 'EventInvitation');
    }


}
