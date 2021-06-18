<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Session;
// use Illuminate\Support\Facades\Log;

const PDFPATH = 'report/pdf/*';

class ReportClassRollsComposer
{
    private $sessionId;
    private $reportId;
    private $sessionName;
    private $heading;
    private $sessions;
    private $attendees;

    public function __construct()
    {
        $this->initializeVariables();
    }

    /**
     * Initialize the courses, sessions, and the arrays for the Blade View
     */
    private function initializeVariables() {
        $this->heading = 'Class Contact Details';
        $this->initializeSessions();
        $this->attendees    = array();
        $this->reportId     = request()->route('id');
        // Log::info('report '.$this->reportId,['user' => auth()->user()->name]);
    }

    /**
     * Initialize courses
     * 
     * Set courseId to the GET['courseId'] variable, or, if not set, the first course in alphabetical order
     */
    private function initializeSessions() {
        $this->sessionId   = request()->get('sessionId',-1);
        $this->sessionId   = (filter_var($this->sessionId, FILTER_VALIDATE_INT) !== false) ? $this->sessionId : -1;   
        $this->sessionName = Session::find($this->sessionId);
        $this->sessionName = (($this->sessionId < 0)or (empty($this->sessionName))) ? 'All '.$this->heading : $this->sessionName->name;
        if (request()->is(PDFPATH) and ($this->sessionId > 0)) {
            $this->sessions    = Session::orderBy('name')->where('sessions.id',$this->sessionId)->where('deleted',0)->where('suspended',0)->get();
            $this->sessions    = Session::join('courses','courses.id','sessions.course_id')
                ->orderBy('sessions.name')->where('sessions.id',$this->sessionId)->where('sessions.deleted',0)->where('sessions.suspended',0)
                ->where('courses.deleted',0)->where('courses.suspended',0)
                ->select('sessions.*')->get();
        } else {
            $this->sessions    = Session::join('courses','courses.id','sessions.course_id')
                ->orderBy('sessions.name')->where('sessions.deleted',0)->where('sessions.suspended',0)
                ->where('courses.deleted',0)->where('courses.suspended',0)
                ->select('sessions.*')->get();
        }
    }

    public function compose(View $view) {
        if (auth()->user()->can('basic member')) {
            abort(403);
        }
        if (request()->is(PDFPATH)) {
            return (new \App\Exports\ClassRollsPdfExport($this->sessions, $this->heading))->show();
        } else {
            $view->with([
                'sessions'        => $this->sessions,
                'selectedSession' => $this->sessionName,
                'reportId'        => $this->reportId,
                'heading'         => $this->heading
            ]);
        }
    }

}
