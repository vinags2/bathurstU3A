<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Setting;
use App\Session;
use App\Helpers\Utils;

class EditTermDatesComposer
{
    private $setting;
    private $course;
    private $sessionId;
    private $memberId;
    private $current_year;
    // $courseDates is true if the Dates are to be for a course. Otherwise the Dates are for the global settings.
    private $courseDates;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->sessionId = request()->sessionId;
        $this->courseDates = !(is_null($this->sessionId));
        // dd(request(), request()->sessionId, $this->courseDates);
        $this->saveMemberId();
        $this->current_year = Utils::currentYear();
        if ($this->courseDates) {
            $this->getCourse();
            // dd($this->course);
        } else {
            $this->getSetting();
            // dd($this->setting);
        }
    }

    /**
     * If the memberId is a session variable (from using back(view)->with('memberId',...) in PersonController),
     * then save it as a request variable.
     * Otherwise, if user is a basic member, use the member's id,
     * Else get the memberId from the Request variable
     * If all else fails, set the memberId to -1.
     */
    private function saveMemberId() {
        $this->memberId = auth()->user()->person_id;
    }

    /**
     * Initialize the Setting
     */
    private function getSetting() {
        $this->setting = Setting::currentSetting();
    }

    private function padTerms($terms, $number_of_terms) {
        $terms = json_decode($terms,true);
        // dd($terms);
        for ($i = 0; $i < 10-$number_of_terms; $i++) {
            $j = $number_of_terms + $i;
            $terms[$j]['start'] = date('Y-m-d', strtotime("now"));
            $terms[$j]['end'] = date('Y-m-d', strtotime("now"));
        }
        return json_encode($terms);
    }

    /**
     * Initialize the Course
     */
    private function getCourse() {
        $this->course = Session::find($this->sessionId);
    }

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        if ($this->courseDates) {
            $view->with([
                'weeks_in_term'  => $this->setting->weeks_in_term,
                'number_of_terms'  => $this->setting->number_of_terms,
                'currentYear' => $this->current_year,
                'action' => route('storeTermDates'),
                'terms' => $this->setting->terms,
                'sessionId' => $this->sessionId,
            ]);
        } else {
            $view->with([
                'weeks_in_term'  => $this->setting->weeks_in_term,
                'number_of_terms'  => $this->setting->number_of_terms,
                'currentYear' => $this->current_year,
                // 'action' => route('setting.store'),
                'action' => route('storeTermDates'),
                'terms' => $this->padTerms($this->setting->terms, $this->setting->number_of_terms),
                'sessionId' => $this->sessionId,
            ]);
        }
    }
}
