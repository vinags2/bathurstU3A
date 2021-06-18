<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;

class Venue extends Model
{
    use SoftDeletes;
    use UpdateByable;
    //

    /**
     * Get the sessions that are held at the venue
     */
    public function sessions()
    {
        return $this->hasMany('App\Session', 'venue_id');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the person who is the contact for this venue
     */
    public function contact()
    {
        return $this->belongsTo('App\Person', 'person_id');
    }

    /**
     * Get the address for this venue
     */
    public function address()
    {
        return $this->belongsTo('App\Address');
    }
}
