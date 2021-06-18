<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SessionAttendee extends Pivot
{
    //

    /**
     * Get the person who attended the session
     */
    public function attendee()
    {
        return $this->belongsTo('App\Person', 'person_id');
        // return $this->belongsTo('App\Person','id','person_id');
    }
    
    /**
     * Get the people who attended the session
     */
    public function attendees()
    {
        return $this->hasMany('App\Person', 'person_id');
        // return $this->belongsTo('App\Person','id','person_id');
    }
    
    /**
     * Get the session attended
     */
    public function session()
    {
        return $this->belongsTo('App\Session');
        // return $this->belongsTo('App\Session','id','session_id');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }
}
