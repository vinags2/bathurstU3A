<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportsForMailingList extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the report that has a mailing list
     */
    public function report()
    {
        return $this->belongsTo('App\Report');
    }
}
