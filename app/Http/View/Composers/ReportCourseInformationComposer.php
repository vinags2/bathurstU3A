<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Session;

const PDFPATH = 'report/pdf/*';

class ReportCourseInformationComposer
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
        $this->heading  = 'Course Information Sheet';
        $this->sessions = $this->getSql()->get();
        $this->reportId = request()->route('id');
    }

    private function getSql() {
        return Session::select('sessions.day', 'sessions.start', 'sessions.end', 'sessions.name AS name', 'venues.name AS venueName', 'people.name AS facilitator', 'people.phone', 'courses.description')
            ->join('venues','venues.id','sessions.venue_id')
            ->join('courses','courses.id','sessions.course_id')
            ->join('people','people.id','sessions.facilitator')
            ->orderBy('sessions.name')
            ->where('sessions.deleted',0)
            ->where('sessions.suspended',0)
            ->where('courses.deleted',0)
            ->where('courses.suspended',0);
    }

    public function compose(View $view) {
        if (request()->is(PDFPATH)) {
            return (new \App\Exports\CourseInformationPdfExport($this->sessions, $this->heading))->show();
        } else {
            $view->with([
                'reportId' => $this->reportId
            ]);
        }
    }

}
