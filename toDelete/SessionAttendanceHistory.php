<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionAttendanceHistory extends Model
{
    use SoftDeletes;
    public $timestamps = false;


    /**
     * Get the person who attended the session
     */
    public function attendee()
    {
        return $this->belongsTo('App\Person');
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
