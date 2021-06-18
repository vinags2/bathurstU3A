<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Session;

const PDFPATH = 'report/pdf/*';

class ReportTimetableComposer
{
    private $reportId;
    private $heading;
    private $sessions;

    public function __construct()
    {
        $this->initializeVariables();
    }

    /**
     * Initialize the courses, sessions, and the arrays for the Blade View
     */
    private function initializeVariables() {
        $this->heading  = 'Weekly Timetable';
        $this->reportId = request()->route('id');
        $this->initializeSessions();
    }

    /**
     * Initialize the sessions for each day of the week
     * 
     */
    private function initializeSessions() {
        $this->sessions['monday']    = $this->getSql(1)->get();
        $this->sessions['tuesday']   = $this->getSql(2)->get();
        $this->sessions['wednesday'] = $this->getSql(3)->get();
        $this->sessions['thursday']  = $this->getSql(4)->get();
        $this->sessions['friday']    = $this->getSql(5)->get();
    }

    private function getSql($day_of_the_week) {
        return Session::select('sessions.day', 'sessions.start', 'sessions.end', 'sessions.name AS name', 'venues.name AS venueName')
            ->join('venues','venues.id','sessions.venue_id')
            ->join('courses','courses.id','sessions.course_id')
            ->join('people','people.id','sessions.facilitator')
            ->orderBy('sessions.start_time')
            ->where('sessions.deleted',0)
            ->where('sessions.suspended',0)
            ->where('courses.deleted',0)
            ->where('courses.suspended',0)
            ->where('day_of_the_week',$day_of_the_week);
    }

    public function compose(View $view) {
        if (request()->is(PDFPATH)) {
            return (new \App\Exports\TimetablePdfExport($this->sessions, $this->heading))->show();
        } else {
            $view->with([
                'reportId' => $this->reportId
            ]);
        }
    }

}
