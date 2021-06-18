<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Person;

class ReportStatisticsComposer
{
    private $mostActiveMembers;
    private $members;
    private $courses;
    private $totalCourses;
    private $mostPopularCourses;
    private $leastPopularCourses;
    private $venues;
    private $mostPopularVenues;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->initializeMembers();
        $this->initializeCourses();
        $this->initializePopularCourses();
        $this->initializeUnpopularCourses();
        $this->initializeAttendances();
        $this->initializeVenues();
    }

    private function initializeMembers() {
    $this->members  = DB::table('people')
        ->selectRaw('count(*) AS numberOfMembers')
        ->selectRaw('count(case when prefer_email=1 then 1 else NULL end) AS newsletterByEmail')
        ->selectRaw('count(case when prefer_email=0 then 1 else NULL end) AS newsletterByPost')
        ->selectRaw('count(case when (Email is null) or (Email = "") then 1 else NULL end) AS noEmail')
        ->selectRaw('count(case when (any_phone is null) then 1 else NULL end) AS noPhone')
        ->selectRaw('count(case when (residential_address is null) and (postal_address is null) then 1 else NULL end) AS noAddress')
        ->selectRaw('count(case when committee_member != 0 then 1 else NULL end) AS numberOfCommitteeMembers')
        ->where('member',1)
        ->where('deleted',0)
        ->first();
    }

    private function initializeCourses() {
        $this->courses = DB::table('sessions')
            ->selectRaw('count(*) AS numberOfSessions')
            ->selectRaw('count(distinct(venue_id)) AS numberOfActiveVenues')
            ->selectRaw('count(case when day_of_the_week = 1 then 1 else NULL end) AS mondaySessions')
            ->selectRaw('count(case when day_of_the_week = 2 then 1 else NULL end) AS tuesdaySessions')
            ->selectRaw('count(case when day_of_the_week = 3 then 1 else NULL end) AS wednesdaySessions')
            ->selectRaw('count(case when day_of_the_week = 4 then 1 else NULL end) AS thursdaySessions')
            ->selectRaw('count(case when day_of_the_week = 5 then 1 else NULL end) AS fridaySessions')
            ->selectRaw('count(case when date_format(start_time,"%H") > 11 then 1 else NULL end) AS morningSessions')
            ->selectRaw('count(case when date_format(start_time,"%H") < 12 then 1 else NULL end) AS afternoonSessions')
            ->leftJoin('courses', 'courses.id','course_id')
            ->where('courses.suspended',0)
            ->where('sessions.suspended',0)
            ->where('courses.deleted',0)
            ->where('sessions.deleted',0)
            ->first();

        $this->totalCourses = DB::table('sessions')
            ->selectRaw('count(*) AS totalCourses')
            ->first();
    }

    private function initializeAttendances() {
        $this->mostActiveMembers = DB::table('session_attendee')
            ->selectRaw('max(person_id) AS personId')
            ->selectRaw('count(*) AS quantity')
            ->where('confirmed',1)
            ->where('session_attendee.deleted',0)
            ->groupBy('person_id')
            ->orderBy('quantity','desc')
            ->first()
            ;
            $maxQuantity = $this->mostActiveMembers->quantity;
        $this->mostActiveMembers = DB::table('session_attendee')
            ->selectRaw('max(person_id) AS personId')
            ->selectRaw('max(first_name) AS firstName')
            ->selectRaw('max(last_name) AS lastName')
            ->selectRaw('count(*) AS quantity')
            ->leftJoin('people','people.id','person_id')
            ->where('confirmed',1)
            ->where('session_attendee.deleted',0)
            ->groupBy('person_id')
            ->having('quantity','>',$maxQuantity-2)
            ->orderBy('quantity','desc')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ;
    }

    private function initializePopularCourses() {
        $this->mostPopularCourses = DB::table('session_attendee')
            ->select('session_attendee.session_id AS sessionId')
            ->selectRaw('max(sessions.course_id) AS courseId')
            ->selectRaw('max(sessions.name) AS sessionName')
            ->selectRaw('count(*) AS quantity')
            ->leftJoin('sessions','session_attendee.session_id','sessions.id')
            ->leftJoin('courses','courses.id','sessions.course_id')
            ->where('sessions.suspended',0)
            ->where('courses.suspended',0)
            ->where('sessions.deleted',0)
            ->where('courses.deleted',0)
            ->groupBy('session_attendee.session_id')
            ->orderBy('quantity','desc')
            ->limit(5)
            ->get() ;
    }

    private function initializeUnpopularCourses() {
        $this->leastPopularCourses = DB::table('session_attendee')
            ->select('session_attendee.session_id AS sessionId')
            ->selectRaw('max(sessions.course_id) AS courseId')
            ->selectRaw('max(sessions.name) AS sessionName')
            ->selectRaw('count(*) AS quantity')
            ->leftJoin('sessions','session_attendee.session_id','sessions.id')
            ->leftJoin('courses','courses.id','sessions.course_id')
            ->where('sessions.suspended',0)
            ->where('courses.suspended',0)
            ->where('sessions.deleted',0)
            ->where('courses.deleted',0)
            ->groupBy('session_attendee.session_id')
            ->orderBy('quantity','asc')
            ->limit(5)
            ->get() ;
    }

    private function initializeVenues() {
        $this->venues = DB::table('venues')
            ->selectRaw('count(*) AS quantity')
            ->where('deleted',0)
            ->first();
        $this->venues = $this->venues->quantity;

        $this->mostPopularVenues = DB::table('sessions')
            ->select('venue_id')
            ->selectRaw('max(venues.name) AS venueName')
            ->selectRaw('count(*) AS quantity')
            ->leftJoin('courses','courses.id','sessions.course_id')
            ->leftJoin('venues','sessions.venue_id','venues.id')
            ->where('sessions.suspended',0)
            ->where('courses.suspended',0)
            ->where('sessions.deleted',0)
            ->where('courses.deleted',0)
            ->groupBy('venue_id')
            ->orderBy('quantity','desc')
            ->limit(4)
            ->get() ;
    }

    public function compose(View $view) {
        // dd($this->mostPopularVenues);
        if (auth()->user()->can('basic member')) {
            abort(403);
        }
        $view->with([
            'mostActiveMembers'  => $this->mostActiveMembers,
            'members'              => $this->members,
            'courses'              => $this->courses,
            'totalCourses'        => $this->totalCourses,
            'mostPopularCourses' => $this->mostPopularCourses,
            'leastPopularCourses'=> $this->leastPopularCourses,
            'venues'               => $this->venues,
            'mostPopularVenues'  => $this->mostPopularVenues,
        ]);
    }
}
