<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportLookupTable extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this Lookup Table
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
