<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Course;
use App\Setting;
use App\Session;
use App\Helpers\Utils;

class EditCoursesComposer
{
    private $state;
    private $showDetails;
    private $currentYear;

    private $course;
    private $courseId;

    private $sessions;
    private $activeTerms;
    private $allYearInsteadOfTerms;
    private $numberOfTerms;
    private $numberOfSessions;
    private $numberOfNewSessions;
    private $facilitators;
    private $alternate_facilitators;
    private $venues;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->saveCourseId();
        $this->state = $this->getState();
        $this->currentYear = Utils::currentYear();
        $this->getCourse();
        $this->getShowDetails();
        if ($this->showDetails) {
            $this->getNumberOfNewSessions();
            $this->getSessions();
            $this->getNewSessions();
            $this->venues = $this->getLinkedModel([$this, "venueModel"], [$this,"venueId"]);
            $this->facilitators = $this->getLinkedModel([$this, "facilitatorModel"], [$this,"facilitatorId"]);
            $this->alternate_facilitators = $this->getLinkedModel([$this, "alternateFacilitatorModel"], [$this,"alternateFacilitatorId"]);
        }
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

    private function getShowDetails() {
            $this->showDetails = $this->course->name != "";
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
        if (($this->state == 'course search') or ($this->state == 'new course')) {
            $this->course = new Course;
            $this->course->id = -1;
            $this->course->name = ucfirst(strtolower(request('toSearchFor')));
            $this->course->description = '';
            $this->course->comment = '';
            $this->suspended = 0;
        } else {
            $this->course = Course::find($this->courseId);
        }
    }

    /**
     * set the number of new sessions
     * Equal to old(numberOfNewSessions) if it exists (from user clicking 'back)
     * or 1 if from user clicking 'new sessions'
     * or 1 if a new course
     * or 0 otherwise
     */
    private function getNumberOfNewSessions() {
        $this->numberOfNewSessions = old('numberOfNewSessions', -1);
        if ($this->numberOfNewSessions == -1) {
            if (request()->filled('new') or ($this->course->id == -1)) {
                $this->numberOfNewSessions = 1;
            } else {
                $this->numberOfNewSessions = 0;
            }
        }
    }

    /**
     * get the sessions associated with the course
     * 
     * if a new course, set the number of new sessions to 1
     */
    private function getSessions() {
        $this->numberOfTerms = Setting::currentSetting()->number_of_terms;
        if (($this->course->id == -1)) {
            $this->sessions = null;
            $this->numberOfSessions = 0;
        } else {
            $this->sessions = $this->course->sessions()->get();
            $this->numberOfSessions = count($this->sessions);
            foreach ($this->sessions as $key => $session) {
                $this->activeTerms[$key] = $session->active_terms_as_array;
                $this->allYearInsteadOfTerms[$key] = in_array(1,$this->activeTerms[$key]) ? 0 : 1;
            }
        }
    }

    /**
     * append 'number of new sessions' to the sessions array, setting
     * the following attibutes of the new session to that of the first session:
     * facilitator, alternate facilitator and venie.
     * name = course name n where n = the number of the sessions
     */
    private function getNewSessions() {
        for ($i=0; $i < $this->numberOfNewSessions; $i++) {
            $totalSessions = $this->numberOfSessions + $i;
            if ($totalSessions > 6) break;
            $this->sessions[$totalSessions] = new Session();
            $this->sessions[$totalSessions]->name = $this->course->name.' '.($totalSessions + 1);
            $this->sessions[$totalSessions]->facilitator = $this->sessions[0] ? $this->sessions[0]->facilitator : null;
            $this->sessions[$totalSessions]->alternate_facilitator = $this->sessions[0] ? $this->sessions[0]->alternate_facilitator : null;
            $this->sessions[$totalSessions]->venue_id = $this->sessions[0] ? $this->sessions[0]->venue_id : null;
            $this->activeTerms[$totalSessions] = $this->sessions[0]->active_terms_as_array;
            $this->allYearInsteadOfTerms[$totalSessions] = 0;
        }
    }

    /**
     * get the effective_from date (that is, from when the changes will take effect)
     * 
     * if in the last term of the year, set date to 1 Jan of the following year
     * otherwise set the date to the first day of the next term
     */
    private function getEffectiveFromDate() {
        $nextYear = $this->currentYear + 1;
        $relativeTermDates = Setting::termDatesComparedToToday();
        if ($relativeTermDates->numberOfTerms ==  $relativeTermDates->term) {
            return $nextYear . '/01/01';
        }
        return $relativeTermDates->nextTermStartDate;
    }

    /**
     * get the model's data for the sessions (eg venues, facilitators, etc)
     * 
     * $linkedModelVariable = the class variable of the model eg $this->venues
     * $thisLinkedModel($session) = returns the model associated with this session (eg $session->venue)
     * $newArrayItemId($session) = returns the id of the model for the current session
     * 
     * Assumes that the name field of the model is called 'name'
     * 
     * returns the data to be saved in $this->venues, etc.
     */
    private function getLinkedModel($thisLinkedModel, $newArrayItemId) {
            $sessions = $this->sessions;
            $linkedModelVariable = [];
            $firstId = -1;
            $firstName = '';
        if ($this->sessions) {
            foreach ($sessions as $key => $session) {
                $thisModel = $thisLinkedModel($session)->first();
                $thisModelName = $thisModel ? $thisModel->name : null;
                $linkedModelVariable[$key] = (object)['id' => $newArrayItemId($session), 'name' => $thisModelName];
                if ($key == 0) {
                    $firstId = $newArrayItemId($session);
                    $firstName = $thisModelName;
                }
            }
        }
            for ($i=0; $i < $this->numberOfNewSessions; $i++) {
                $totalSessions = $this->numberOfSessions + $i;
                if ($totalSessions > 6) break;
                $linkedModelVariable[$totalSessions] = (object)['id' => $firstId, 'name' => $firstName];
            }
        return $linkedModelVariable;
    }

    /**
     * Callback functions for GetLinkedModel.
     */
    private function venueModel($session) {
        return $session->venue();
    }

    private function venueId($session) {
        return $session->venue_id;
    }

    private function facilitatorModel($session) {
        return $session->facilitator_details();
    }

    private function facilitatorId($session) {
        return $session->facilitator;
    }

    private function alternateFacilitatorModel($session) {
        return $session->alternate_facilitator_details();
    }

    private function alternateFacilitatorId($session) {
        return $session->alternate_facilitator;
    }

    private function getRollTypeOptions() {
        if ($this->sessions) {
            return $this->sessions[0]->rollTypeOptions();
        }
        return null;
    }

    /** Return the parameters to be passed to the View */
    private function getViewParameters() {
        $viewParameters = [
            'course'                        => $this->course,
            'effectiveFromDate'             => $this->getEffectiveFromDate(),
            'effectiveFromOptions'          => Setting::effectiveFromOptions(),
            'showDetails'                   => $this->showDetails,
            'state'                         => $this->state,
            'currentYear'                   => $this->currentYear,
            'url'                           => url()->current(),
            'searchUrl'                     => url('coursesearch'),
            'effectiveFrom'                 => Setting::effectiveFrom(),
            'paramKey'                      => 'name', // paramKey is passed in the url to the API eg ?name=bonsai
        ];

        if ($this->showDetails) {
            $viewParameters += [
                'sessions'                      => $this->sessions,
                'numberOfSessions'              => $this->numberOfSessions,
                'numberOfNewSessions'           => $this->numberOfNewSessions,
                'facilitators'                  => $this->facilitators,
                'alternate_facilitators'        => $this->alternate_facilitators,
                'venues'                        => $this->venues,
                'rollTypeOptions'               => $this->getRollTypeOptions(),
                'numberOfTerms'                 => $this->numberOfTerms,
                'activeTerms'                   => $this->activeTerms,
                'allYearInsteadOfTerms'         => $this->allYearInsteadOfTerms
            ];
        }

        return $viewParameters;
    }

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        // dd($this->getViewParameters(), request());
        $view->with($this->getViewParameters());
    }
}
