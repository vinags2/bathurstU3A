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

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
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
        ]);
    }
}
