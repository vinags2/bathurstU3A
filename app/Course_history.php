<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Course_history extends Model
{
    public $timestamps = false;
    protected $attributes = [
        'effective_to' => null,
    ];
    //

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the course that this historical record refers to
     */
    public function course()
    {
        return $this->belongsTo('App\Course','id','course_id');
    }
}
