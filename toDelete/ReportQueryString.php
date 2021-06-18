<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportQueryString extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this Query String
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
