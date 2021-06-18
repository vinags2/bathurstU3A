<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportColumnWidth extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this Column Width
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
