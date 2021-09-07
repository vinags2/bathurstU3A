<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session_history extends Model
{
    public $timestamps = false;
    protected $attributes = [
        'effective_to' => null,
    ];
    //

    /**
     * Get the course that this session belongs to
     */
    public function course()
    {
        return $this->belongsTo('App\Course','id','course_id');
    }

    /**
     * Get the venue where this session is held
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue','id','venue_id');
    }

    /**
     * Get the facilitator of this session
     */
    public function facilitator()
    {
        return $this->belongsTo('App\Person','id','facilitator');
    }

    /**
     * Get the alternate facilitator of this session
     */
    public function alternate_facilitator()
    {
        return $this->belongsTo('App\Person','id','alternate_facilitator');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the session that historical record refers to
     */
    public function session()
    {
        return $this->belongsTo('App\Session','id','session_id');
    }
}
