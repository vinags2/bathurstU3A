<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldUser extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the person's details
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
