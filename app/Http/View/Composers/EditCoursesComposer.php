<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Course;
use App\Setting;
use App\Session;
use App\Helpers\Utils;

class EditCoursesComposer
{
    private $course;
    private $currentYear;
    private $courseId;
    private $state;
    private $sessions;
    private $numberOfSessions;
    private $facilitators;
    private $alternate_facilitators;
    private $venues;
    private $maxClassSizes;
    private $minClassSizes;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->saveCourseId();
        $this->currentYear = Utils::currentYear();
        $this->state       = $this->getState();
        $this->getCourse();
        $this->getSessions();
        $this->getFacilitators();
        $this->getAlternateFacilitators();
        $this->getVenues();
        $this->getMaxClassSizes();
    }

    /**
     * If the courseId is a session variable (from using back(view)->with('courseId',...) in CourseController),
     * then save it as a request variable.
     * Get the courseId from the Request variable
     * Otherwise, set the courseId to -1.
     */
    private function saveCourseId() {
        if (session('findMatches')) {
            request()->merge(['courseId'=>session('findMatches')]);
        }
        $this->courseId = request()->get('findMatches',-1);
    }

    /**
     * Is the courseId a valid integer?
     */
    private function isValidInteger() {
        return filter_var($this->courseId, FILTER_VALIDATE_INT) !== false;   
    }

    /**
     * Is the course ID an id of an existing course?
     */
    private function isValidCourse() {
        return !is_null(Course::find($this->courseId));
    }

    /**
     * Is the courseId valid?
     */
    private function isValidCourseId() {
        return $this->isValidInteger() && $this->isValidCourse();
    }

    /**
     * What state is the app in?
     * Possible states are: 'update existing course', 'new course', or 'course search'
     * Used in edit.blade.php (as well as here)
     */
     private function getState() {
         if (request()->filled('findMatches') && $this->isValidCourseId()) {
             return 'update existing course';
         }
         if (request()->filled('newModel')) {
             return 'new course';
         }
         return 'course search';
     }

    /**
     * Initialize the Course
     */
    private function getCourse() {
        if ($this->state == 'course search') {
            $this->course = new Course;
            $this->course->id = -1;
            $this->course->name = ucfirst(strtolower(request()->input('course_name', null)));
            $this->course->description = '';
            $this->course->comment = '';
            $this->suspended = 0;
        } else {
            $this->course = Course::find($this->courseId);
        }
    }

    private function getSessions() {
        if (($this->state == 'course search') or ($this->course->id == -1)) {
            $this->sessions = null;
            $this->numberOfSessions = 0;
        } else {
            $this->sessions = $this->course->sessions()->get();
            $this->numberOfSessions = count($this->sessions);
        }
    }

    /**
     * get the effective_from date
     * 
     * if in the last term of the year, set date to 1 Jan of the following year
     * otherwise set the date to the day before the first day of the next term
     */
    private function getEffectiveFromDate() {
        $nextYear = $this->currentYear + 1;
        return $nextYear . '/01/01';
    }

    private function padSessions($sessions, $number_of_sessions) {
        for ($i = 0; $i < 6-$number_of_sessions; $i++) {
            $j = $number_of_sessions + $i;
            $sessions[$j] = new Session();
        }
        return $sessions;
    }

    private function padArraysWithNullObjects($arrays, $singleArray = false) {
        if ($singleArray) {
            $nullArray = 0;
        } else {
            $nullArray = json_encode(['id' => null, 'name' => null]);
        }
        for ($i = 0; $i < 6-$this->numberOfSessions; $i++) {
            $j = $this->numberOfSessions + $i;
            $arrays[$j] =  $nullArray;
        }
        return $arrays;
    }

    private function getVenues() {
        if ($this->sessions) {
            $sessions = $this->sessions;
            $this->venues = [];
            foreach ($sessions as $key => $session) {
                $venue = $session->venue()->first();
                $venue_name = $venue ? $venue->name : null;
                $this->venues[$key] = json_encode((object)['id' => $session->venue_id, 'name' => $venue_name]);
            }
        }
        $this->venues = $this->padArraysWithNullObjects($this->venues);
    }

    private function getFacilitators() {
        if ($this->sessions) {
            $sessions = $this->sessions;
            $this->facilitators = [];
            foreach ($sessions as $key => $session) {
                $facilitator = $session->facilitator_details()->first();
                $facilitator_name = $facilitator ? $facilitator->name : null;
                $this->facilitators[$key] = json_encode((object)['id' => $session->facilitator, 'name' => $facilitator_name]);
            }
        }
        $this->facilitators = $this->padArraysWithNullObjects($this->facilitators);
    }

    private function getAlternateFacilitators() {
        if ($this->sessions) {
            $sessions = $this->sessions;
            $this->alternate_facilitators = [];
            foreach ($sessions as $key => $session) {
                $alternate_facilitator = $session->alternate_facilitator_details()->first();
                $alternate_facilitator_name = $alternate_facilitator ? $alternate_facilitator->name : null;
                $this->alternate_facilitators[$key] = json_encode((object)['id' => $session->alternate_facilitator, 'name' => $alternate_facilitator_name]);
            }
        }
        $this->alternate_facilitators = $this->padArraysWithNullObjects($this->alternate_facilitators);
    }

    private function getMaxClassSizes() {
        if ($this->sessions) {
            $sessions = $this->sessions;
            $this->maxClassSizes = [];
            foreach ($sessions as $key => $session) {
                $this->maxClassSizes[$key] = $session->maximum_session_size;
            }
        }
        $this->maxClassSizes = $this->padArraysWithNullObjects($this->maxClassSizes, true);
    }

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        // dd($this->facilitators[0], (object)$this->facilitators[0],
        //  $this->alternate_facilitators, (object) $this->alternate_facilitators,
        //  (object) $this->venues, $this->venues);
        //  $temp = (object) $this->facilitators;
        // dd(json_encode($this->alternate_facilitators));
        // dd($this->padSessions($this->sessions, $this->numberOfSessions));
        $view->with([
            'course'                        => $this->course,
            'effectiveFromDate'             => $this->getEffectiveFromDate(),
            'showDetails'                   => ($this->course->name != ""),
            'state'                         => $this->state,
            'currentYear'                   => $this->currentYear,
            'url'                           => url()->current(),
            'sessions'                      => $this->padSessions($this->sessions, $this->numberOfSessions),
            'searchUrl'                     => url('coursesearch'),
            'paramKey'                      => 'name', // paramKey is passed in the url to the API eg ?name=bonsai
            'allowNewModel'                 => true, // allow user to select a non-existing model/course
            'effectiveFrom'                 => Setting::effectiveFrom(),
            'numberOfSessions'              => $this->numberOfSessions,
            'facilitators'                  => $this->facilitators,
            'alternate_facilitators'        => $this->alternate_facilitators,
            'venues'                        => $this->venues,
            'effectiveFromOptions'          => Setting::effectiveFromOptions(),
            'max_class_sizes'               => $this->maxClassSizes
        ]);
    }
}
