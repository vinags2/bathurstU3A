<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Setting;
use App\Helpers\Utils;

class EditTermDatesComposer
{
    private $setting;
    private $memberId;
    private $current_year;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->saveMemberId();
        $this->current_year = Utils::currentYear();
        $this->getSetting();
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

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        $view->with([
            'settings' => $this->setting,
            // 'weeks_in_term'  => $this->setting->weeks_in_term,
            // 'weeks_in_term'  => request('weeks_in_term'),
            // 'number_of_terms'  => request('number_of_terms'),
            // 'rejoin_start_date' => request('rejoin_start_date'),
            'currentYear' => $this->current_year,
            'action' => route('setting.store'),
        ]);
    }
}
