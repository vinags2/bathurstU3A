<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportSql extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the reports that use this SQL
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }

    /**
     * Get the reports that use this SQL
     */
    public function reports_for_another_year()
    {
        return $this->hasMany('App\Report','id','report_sql_id_for_another_year');
    }
}
