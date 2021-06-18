<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;
use App\Traits\Sanitizable;

class Address extends Model
{
    use SoftDeletes;
    use UpdateByable;
    use Sanitizable;

    protected $guarded = ['id', 'address'];

    /**
     * firstOrCreate, with sanitization of the data
     */
    static public function myFirstOrCreate(array $searchData) {
        if (empty($searchData['line_1']) and empty($searchData['line_2'])) {
            return static::nullAddress();
        }
        $searchData = static::sanitize($searchData);
        return Address::firstOrCreate($searchData);
    }

    static private function nullAddress() {
        return new Address(['id' => null, 'line_1' => 'not found', 'line_2' => 'not found']);
    }

    static private function sanitize(array $data) {
        return static::addressSanitizable($data);
    }

    /**
     * Get the people who live at an address
     */
    public function residents()
    {
        return $this->hasMany('App\Person','residential_address');
    }

    /**
     * Get the people who receive post at this address
     */
    public function addressees()
    {
        return $this->hasMany('App\Person','postal_address');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the people who receive post at this address
     */
    public function venues()
    {
        return $this->hasMany('App\Venue');
    }
}
