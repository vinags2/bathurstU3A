<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Course;
use App\SessionAttendee;
use App\Helpers\Utils;

class ReportCourseDetailsComposer
{
    private $courseId;
    private $courses;
    private $course;
    private $sessions;
    private $venues;
    private $facilitators;
    private $terms;
    private $rollTypes;
    private $maximumSizes;
    private $minimumSizes;
    private $attendees;
    private $currentYear;

    public function __construct()
    {
        $this->initializeVariables();
    }

    /**
     * Initialize the courses, sessions, and the arrays for the Blade View
     */
    private function initializeVariables() {
        $this->currentYear = Utils::currentYear();
        $this->initializeCourses();
        $this->sessions     = $this->course->sessions()->get();
        $this->venues       = array();
        $this->facilitators = array();
        $this->terms        = array();
        $this->rollTypes    = array();
        $this->maximumSizes = array();
        $this->minimumSizes = array();
        $this->attendees    = array();
    }

    /**
     * Initialize courses
     * 
     * Set courseId to the GET['courseId'] variable, or, if not set, the first course in alphabetical order
     */
    private function initializeCourses() {
        $this->courseId = request()->get('courseId',-1);
        $this->courseId = (filter_var($this->courseId, FILTER_VALIDATE_INT) !== false) ? $this->courseId : -1;   
        $this->courses  = Course::orderBy('name')->get();
        $this->courseId = ($this->courseId < 0) ? $this->courses[0]->id : $this->courseId;
        $this->course   = Course::find($this->courseId);
    }

    public function compose(View $view) {
        $this->sessionData();
        $view->with([
            'sessions'     => $this->sessions,
            'venues'       => $this->venues,
            'facilitators' => $this->facilitators,
            'terms'        => $this->terms,
            'roll_types'   => $this->rollTypes,
            'minimumSizes' => $this->minimumSizes,
            'maximumSizes' => $this->maximumSizes,
            'attendees'    => $this->attendees,
            'courses'      => $this->courses,
            'course'       => $this->course,
            'memberDetailsReport' => auth()->user()->hasPermissionTo('basic member') ? 56 : null
        ]);
    }

    private function sessionData() {
        foreach ($this->sessions as $key => $session) {
            $this->venueData($session);
            $this->facilitatorData($session);
            $this->termData($session);
            $this->rollData($session);
            $this->classSizeData($session);
            $this->attendeeData($session, $key);
        }
    }

    private function venueData($session) {
        $venue          = $session->venue()->first();
        $this->venues[] = ['name' => !empty($venue->name) ? $venue->name : '(unkown)', 'id' => !empty($venue->id) ? $venue->id : null] ;
    }

    private function facilitatorData($session) {
        $facilitator          = $session->facilitator_details()->first();
        $this->facilitators[] = $facilitator;
    }

    private function termData($session) {
        if ($session->follows_term) {
            $this->terms[] = ($session->term_length ?? 'unspecified number of'). ' weeks';
        } else {
            $this->terms[] = 'The facilitator follows his/her own timetable';
        }
    }

    private function rollData($session) {
        if ($session->roll_type & 16) {
            $temp = "no roll required";
        } else {
            if ($session->roll_type & 1) {
                $temp = "generic";
            } else {
                $temp = "normal";
            }
            if ($session->roll_type & 2) {
                $temp .= ",between terms roll";
            }
            if ($session->roll_type & 4) {
                $temp .= ",no extra blank pages";
            }
            if ($session->roll_type & 8) {
                $temp .= ",only one required for all sessions combined";
            }
            if ($session->roll_type & 32) {
                $temp .= ",monthly roll";
            }
            if ($session->roll_type & 64) {
                $temp .= ",no contact details sheet required";
            }
        }
        $this->rollTypes[] = $temp;
    }

    private function classSizeData($session) {
        $this->maximumSizes[] = $session->maximum_session_size;
        $this->minimumSizes[] = $session->minimum_session_size;
    }

    private function attendeeData($session, $key) {
        // TODO: order session attendees by name
        $session_attendees = SessionAttendee::where('session_id', $session->id)
            ->where('year',$this->currentYear)
            ->get();
        foreach ($session_attendees as $session_attendee) {
            $attendee                = $session_attendee->attendee()->first();
            $this->attendees[$key][] = $attendee;
        }
    }
}
