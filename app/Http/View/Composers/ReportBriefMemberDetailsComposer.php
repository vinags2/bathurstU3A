<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Person;

class ReportBriefMemberDetailsComposer
{
    private $memberId;
    private $user;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->initializeMembers();
    }

    /**
     * Initializw the memberId
     *  
     * If no member id is passed in the URL, use the logged in user instead
     */
    private function initializeMembers() {
        $user = auth()->user();
        $memberId       = request()->get('memberId',$user->person_id);
        $this->memberId = (filter_var($memberId, FILTER_VALIDATE_INT) !== false) ? $memberId : auth()->user()->person_id;   
        $this->user     = Person::find($this->memberId);
    }

    public function compose(View $view) {
        $view->with([
            'user'  => $this->user,
        ]);
    }
}
