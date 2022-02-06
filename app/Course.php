<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Sanitizable;
use App\Traits\UpdateByable;

class Course extends Model
{
    use SoftDeletes;
    use UpdateByable;
    //

    /**
     * updateOrCreate, with sanitization of the data
     */
    static public function myUpdateOrCreate(array $searchData, array $extraData) {
        $searchData = static::trimSanitize($searchData);
        $extraData  = static::trimSanitize($extraData);
        // dd($searchData, $extraData);
        $course = Course::updateOrCreate($searchData, $extraData);
        // now update course_histories
        return $course;
    }

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
