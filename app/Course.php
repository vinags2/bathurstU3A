<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;

class Course extends Model
{
    use SoftDeletes;
    use UpdateByable;
    //

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the course's sessions
     */
    public function sessions()
    {
        return $this->hasMany('App\Session');
    }
}
