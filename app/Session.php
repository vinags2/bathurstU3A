<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;

class Session extends Model
{
    use SoftDeletes;
    use UpdateByable;
    //

    /**
     * Get the people who have attended the session historically
     */
    // public function historical_roll()
    // {
    //     return $this->hasMany('App\SessionAttendanceHistory');
    // }

    /**
     * Get the people who attended the session
     */
    public function roll()
    {
        return $this->hasMany('App\SessionAttendee');
        // return $this->hasMany('App\SessionAttendee','session_id','id');
    }

    /**
     * Get the course for the session
     */
    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    /**
     * Get the venue for the session
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue', 'venue_id');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the person who facilitates the session
     * Note that I've got this working...'facilitator' is the key in this table, ie the Session table
     *  for a 'belongsTo' relationship.
     */
    public function facilitator_details()
    {
        return $this->belongsTo('App\Person','facilitator');
    }

    /**
     * Get the person who is a backup facilitator for the session
     */
    public function alternate_facilitator_details()
    {
        return $this->belongsTo('App\Person','alternate_facilitator');
    }
}
