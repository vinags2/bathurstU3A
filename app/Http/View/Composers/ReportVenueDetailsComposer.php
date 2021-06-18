<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Venue;

class ReportVenueDetailsComposer
{
    private $venueId;
    private $venue;
    private $contact;
    private $venues;
    private $address;
    private $sessions;
    private $oldSessions;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->venues();
        $this->contact();
        $this->address();
        $this->sessions();
        $this->oldSessions();
    }

    private function venues() {
        $this->venueId = request()->get('venueId',-1);
        $this->venueId = (filter_var($this->venueId, FILTER_VALIDATE_INT) !== false) ? $this->venueId : -1;   
        $this->venues  = Venue::orderBy('name')->get();
        $this->venueId = ($this->venueId < 0) ? $this->venues[0]->id : $this->venueId;
        $this->venue   = Venue::find($this->venueId);
    }

    private function contact() {
        $this->contact = $this->venue->contact()->first();
    }

    private function address() {
        $this->address = $this->venue->address()->first();
        $this->address = (!empty($this->address)) ? $this->address : null;
    }

    private function sessions() {
        // TODO: make scopes for the following query
        $classes = $this->venue->sessions()->where('deleted',0)->where('suspended',0)->orderBy('day_of_the_week')->orderBy('start_time')->orderBy('end_time')->get();
        $this->sessions = [];
        foreach ($classes as $class) {
            $this->sessions[] = ['name' => $class->name,'day of week' => $class->day,'start time' => $class->start,'end time' => $class->end, 'href' => $class->course_id];
        }
    }

    private function oldSessions() {
        // TODO: make scopes for the following query
        $oldClasses = $this->venue->sessions()->where(function ($query) {
                $query->where('sessions.deleted',1)->orWhere('sessions.suspended',1);
            })->orderBy('day_of_the_week')->orderBy('start_time')->orderBy('end_time')->get();
        $this->oldSessions = [];
        foreach ($oldClasses as $class) {
            $this->oldSessions[] = ['name' => $class->name,'day of week' => $class->day,'start time' => $class->start,'end time' => $class->end];
        }
    }

    public function compose(View $view) {
        $view->with([
            'venue'       => $this->venue,
            'address'     => $this->address,
            'venues'      => $this->venues,
            'contact'     => $this->contact,
            'sessions'    => $this->sessions,
            'oldSessions' => $this->oldSessions
        ]);
    }
}
