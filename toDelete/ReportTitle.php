<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportTitle extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this Title
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
