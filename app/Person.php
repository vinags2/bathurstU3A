<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdateByable;
use App\Traits\Sanitizable;
use App\Helpers\Utils;

class Person extends Model
{
    use SoftDeletes;
    use UpdateByable;
    use Sanitizable;

    protected $guarded = ['id', 'name', 'any_phone'];

    /**
     * updateOrCreate, with sanitization of the data
     */
    static public function myUpdateOrCreate(array $searchData, array $extraData) {
        if (empty($searchData['first_name']) or empty($searchData['last_name'])) {
            return static::nullPerson();
        }
        $searchData = static::sanitize($searchData);
        $extraData  = static::sanitize($extraData);
        // dd($searchData, $extraData);
        $person = Person::updateOrCreate($searchData, $extraData);
        // $membershipRecord = Person::myUpdateOrCreateMembershipRecord($person);
        // return [$person, $membershipRecord];
        return $person;
    }

    /**
     * updateOrCreate, with sanitization of the data
     */
    static public function myUpdateOrCreateContactDetailsOnly(array $searchData, array $extraData) {
        if (empty($searchData['first_name']) or empty($searchData['last_name'])) {
            return static::nullPerson();
        }
        $searchData = static::sanitize($searchData);
        // dd($searchData, $extraData);
        $person = Person::updateOrCreate($searchData, $extraData);
        // $membershipRecord = Person::myUpdateOrCreateMembershipRecord($person);
        // return [$person, $membershipRecord];
        return $person;
    }

    /**
     * Store the related Membership Record (date of joining, etc)
     */
    static public function myUpdateOrCreateMembershipRecord(Person $person) {
        // if (request()-filled('join') || $request()->filled('renew')) {
        //     MembershipHistory::myFirstOrCreate($person->id, 'renew');
        // } elseif (request()->filled('revoke')) {
        //     MembershipHistory::myFirstOrCreate($person->id, 'revoke');
        // }

    }

    static private function nullPerson() {
        return new Person(['id' => null, 'first_name' => 'not found', 'last_name' => 'not found']);
    }

    static private function sanitize(array $data) {
        return static::nameSanitizable($data);
    }

    /**
     * Get records that have names that sound like a name
     */
    public function scopeCloseMatchingNames($query, $first_name, $last_name) {
        return $query->where('first_name','sounds like', $first_name)
            ->where('last_name','sounds like',$last_name)
            ->where('name','<>',$first_name.' '.$last_name);
    }

    /**
     * Get records that have names that sound like a name or exactly match the name
     */
    public function scopeCloseAndExactMatchingNames($query, $first_name, $last_name) {
        return $query->where('first_name','sounds like', $first_name)
            ->where('last_name','sounds like',$last_name);
    }


    /**
     * Get the person's partner record
     */
    public function partner()
    {
        return $this->hasOne('App\Person','partner','id');
    }

    /**
     * Get the person's User record
     */
    public function user()
    {
        return $this->hasOne('App\User','id','person_id');
    }

    /**
     * Get the residential address of the person
     */
    public function residential_address_details()
    {
        return $this->belongsTo('App\Address','residential_address');
    }

    /**
     * Get the postal address of the person
     */
    public function postal_address_details()
    {
    return $this->belongsTo('App\Address','postal_address');
    }

    /**
     * Get the person's emergency contact record
     */
    public function emergency_contact()
    {
        return $this->hasOne('App\Person','id','emergency_contact');
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Get the records that the person has updated
     */
    public function person_updates()
    {
        return $this->hasMany('App\Person','updated_by');
    }

    /**
     * Get the records of the Address Table that the person has updated
     */
    public function address_updates()
    {
        return $this->hasMany('App\Address','updated_by');
    }

    /**
     * Get the records of the MembershipHistory Table that the person has updated
     */
    public function membership_history_updates()
    {
        return $this->hasMany('App\MembershipHistory','updated_by');
    }


    /**
     * Get the records of the SessionAttendee Table that the person has updated
     */
    public function sessionattendee_updates()
    {
        return $this->hasMany('App\SessionAttendee','updated_by');
    }

    /**
     * Get the records of the SessionAttendee Table that the person has updated
     */
    // public function historical_sessionattendee_updates()
    // {
    //     return $this->hasMany('App\SessionAttendanceHistory','updated_by');
    // }

    /**
     * Get the historical attendance records of the person
     */
    // public function historical_attendance_records()
    // {
    //     return $this->hasMany('App\SessionAttendanceHistory');
    // }

    /**
     * Get the attendance records of the person
     */
    public function attendance_records()
    {
        return $this->hasMany('App\SessionAttendee');
    }

    /**
     * Get the records of the Address Table that the person has updated
     */
    public function course_updates()
    {
        return $this->hasMany('App\Course','updated_by');
    }

    /**
     * Get the records of the Session Table that the person has updated
     */
    public function session_updates()
    {
        return $this->hasMany('App\Session','updated_by');
    }

    /**
     * Get the sessions that the person facilitates
     * Note that I've got this working...'facilitator' is the key in the 'other' table, ie Session
     *  for a hasMany relationship.
     */
    public function facilitates()
    {
        return $this->hasMany('App\Session','facilitator');
    }

    /**
     * Get the sessions that the person is a backup facilitator
     */
    public function facilitates_as_a_backup()
    {
        return $this->hasMany('App\Session','alternate_facilitator');
    }

    /**
     * Get the log entries for this person
     */
    public function logs()
    {
        return $this->hasMany('App\Log');
    }

    /**
     * Get the membership history of this person
     */
    public function membership_records()
    {
        return $this->hasMany('App\MembershipHistory');
    }
   
    /**
     * Get the user's details from the Old User Table
     */
    public function old_details_from_v1_tables()
    {
        return $this->hasOne('App\OldUser');
    } 

    /**
     * Get the records of the Venues Table that the person has updated
     */
    public function venue_updates()
    {
        return $this->hasMany('App\Venue','updated_by');
    }

    /**
     * Get the venues that this person is the contact for
     */
    public function venues()
    {
        return $this->hasMany('App\Venue', 'person_id');
    }

    ////////////////
    // Attributes //
    ////////////////

    /**
     * Is the person a financial member?
     */
    public function getIsMemberAttribute() {
        return $this->membership_records()->where('year',Utils::currentYear())->count() > 0;
    }

    /**
     * Is the person a financial member the previous year?
     */
    public function getIsMemberPreviousYearAttribute() {
        return $this->membership_records()->where('year',Utils::currentYear()-1)->count() > 0;
    }

    /**
     * Is the person a financial member the following year?
     */
    public function getIsMemberNextYearAttribute() {
        return $this->membership_records()->where('year',Utils::currentYear()+1)->count() > 0;
    }

    /**
     * Is the person a non-member (ie a contact for venue or emergency for example)
     * A contact is defined as not being a member for the current and previous years.
     */
    public function getIsContactOnlyAttribute() {
        return !$this->is_member && !$this->is_member_previous_year;
    }
}
