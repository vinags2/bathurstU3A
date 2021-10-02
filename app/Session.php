<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;
use App\Setting;
use Carbon\Carbon;

class Session extends Model
{
    use SoftDeletes;
    use UpdateByable;
    //
    protected $appends = ['print_contact_details', 'roll_only'];

    protected $attributes = [
        'day_of_the_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '10:30:00',
        'week_of_the_month' => 0,
        'active_terms' => 0,
        'roll_type' => 64
    ];

    /**
     * Get the people who have attended the session historically
     */
    // public function historical_roll()
    // {
    //     return $this->hasMany('App\SessionAttendanceHistory');
    // }

    /**
     * Get the people who attended the session
     */
    public function roll()
    {
        return $this->hasMany('App\SessionAttendee');
        // return $this->hasMany('App\SessionAttendee','session_id','id');
    }

    /**
     * Get the course for the session
     */
    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    /**
     * Get the venue for the session
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue', 'venue_id');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the person who facilitates the session
     * Note that I've got this working...'facilitator' is the key in this table, ie the Session table
     *  for a 'belongsTo' relationship.
     */
    public function facilitator_details()
    {
        return $this->belongsTo('App\Person','facilitator');
    }

    /**
     * Get the person who is a backup facilitator for the session
     */
    public function alternate_facilitator_details()
    {
        return $this->belongsTo('App\Person','alternate_facilitator');
    }

    /**
     * Return the 'print contact details' component of roll_type 
     * Return the roll type without the 'include contact details' component
     * [Note that the roll type is an integer, with 64 added if the rolls are 
     * to be printed with the contact details of the members]
     */
    public function getPrintContactDetailsAttribute() {
        return ($this->roll_type / 64) >= 1 ? 1 : 0;
    }

    /**
     * output start and end times in the format H:i (2 digits for 24 hour and minutes)
     */
    // public function getStartTimeAttribute() {
        // return (new DateTime($this->start_time))->format('H:i');
    // }

    /**
     * Return the roll component of roll_type 
     * [Note that the roll type is an integer, with 64 added if the rolls are 
     * to be printed with the contact details of the members]
     */
    public function getRollOnlyAttribute() {
        return $this->roll_type % 64;
    }

    /**
     * return the active terms as an array of terms with true/false
     * for example for a course not running in term 3, the output would be:
     * [true, true, false, true]
     */
    public function getActiveTermsAsArrayAttribute() {
        $numberOfTerms = Setting::currentSetting()->number_of_terms;
        $terms = [];

        if ($this->active_terms == 0) {
            $activeTerms = 1023; 
        } else {
            $activeTerms = $this->active_terms;
        }

        $n=0;
        while (($activeTerms != 0) and ($n < $numberOfTerms)) {
           $terms[$n++] = $activeTerms % 2;
           $activeTerms = intval($activeTerms/2); 
        }

        return $terms;
    }

    /**
     * Output start and end times as hour and minutes (that is, strip the seconds)
     */
    public function getStartTimeAttribute($value) {
        return substr($value,0,5);
        // return \Carbon\Carbon::createFromFormat('H:i:s', $value)->toTimeString();
    }

    public function getEndTimeAttribute($value) {
        return substr($value,0,5);
        // return \Carbon\Carbon::createFromFormat('H:i:s', $value)->toTimeString();
    }

    /**
     * return an array of objects that can be used in a blade/vue component
     */

    static function rollTypeOptions() {
        $options = [];
        $options[0] = (object) ['key' => '0', 'text' => 'Normal'];
        $options[1] = (object) ['key' => '1', 'text' => 'Generic'];
        $options[2] = (object) ['key' => '4', 'text' => '2 page generic'];
        $options[3] = (object) ['key' => '32', 'text' => 'Monthly'];
        $options[4] = (object) ['key' => '16', 'text' => 'No roll'];
        $options[5] = (object) ['key' => '2', 'text' => 'Between terms roll'];
        return json_encode($options);
    }

    /**
     * return true if the current session is configured with roll of type $rollType
     * where $rollType can be:
     * 'normal', 'generic', 2 page generic', 'monthly', 'no roll', 'between terms roll'
     */
    public function isOfRollType($rollType) {
        foreach (json_decode(Session::rollTypeOptions()) as $option) {
            if (strtolower($option->text) == strtolower($rollType)) {
                if ($option->key == $this->roll_only) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}
