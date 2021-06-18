<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportPageName extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this Page Name
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
