<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;
    //

    /**
     * Get the SQL for this report
     */
    public function sql()
    {
        return $this->belongsTo('App\ReportSql');
    }

    /**
     * Get the SQL for this report
     */
    public function sql_for_another_year()
    {
        return $this->belongsTo('App\ReportSql','report_sql_id_for_another_year');
    }

    /**
     * Get the SQL for this report
     */
    public function column_width()
    {
        return $this->belongsTo('App\ReportColumnWidth');
    }

    /**
     * Get the SQL for this report
     */
    public function page_name()
    {
        return $this->belongsTo('App\ReportPageName','report_page_name_id','id');
    }

    /**
     * Get the SQL for this report
     */
    public function query_string()
    {
        return $this->belongsTo('App\ReportQueryString');
    }

    /**
     * Get the SQL for this report
     */
    public function lookup_table()
    {
        return $this->belongsTo('App\ReportLookupTable');
    }

    /**
     * Get the SQL for this report
     */
    public function title()
    {
        return $this->belongsTo('App\ReportTitle');
    }

    /**
     * Get the mailing list details for this report
     */
    public function mailing_list()
    {
        return $this->hasOne('App\Report');
    }
}
