<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //

    /**
     * Get the person who updated the record
     */
    public function logged_by()
    {
        return $this->belongsTo('App\Person');
    }
}
