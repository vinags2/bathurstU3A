<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;
use App\Helpers\Utils;

class MembershipHistory extends Model
{
    use SoftDeletes;
    use UpdateByable;
    protected $guarded = ['id'];


    /**
     * Get the person who is a member of this historical membership record
     */
    public function member()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

}

