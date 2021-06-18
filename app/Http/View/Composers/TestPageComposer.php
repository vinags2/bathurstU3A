<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Person;
use App\Helpers\Utils;

class TestPageComposer
{
    private $person;
    private $memberId;
    private $emergency_contact;
    private $payment_method;
    private $newsletter_delivery;
    private $committee_member;
    private $postal_address;
    private $current_year;
    private $membership_history;
    private $current_member;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->saveMemberId();
        $this->current_year = Utils::currentYear();
        // session(['currentYear' => 1952]);
        // request()->merge(['currentYear' => 1953]);
        // config(['myconfig.currentYear' => 2020]);
        $this->getPerson();
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
     * Initialize the Person
     */
    private function getPerson() {
        $this->person = Person::find($this->memberId);
        $this->addresses();
        $this->emergency_contact();
        $this->payment_method();
        $this->newsletter_delivery();
        $this->committee_member();
        $this->membership_history();
        $this->isCurrentMember();
    }

    /**
     * Get the emergency contact of the person
     */
    private function emergency_contact() {
        $this->emergency_contact = $this->person->emergency_contact()->first();
    }

    /**
     * Get the address of the person
     */
    private function addresses() {
        $this->postal_address      = $this->person->postal_address_details()->first();
    }

    /**
     * Get the pament method of the person
     */
    private function payment_method() {
        $this->payment_method = $this->person ?? $this->person->payment_method ?? -1;
    }

    /**
     * Get the Prefer Email of the person
     */
    private function newsletter_delivery() {
        $this->newsletter_delivery = $this->person ?? $this->person->prefer_email;
    }

    /**
     * Get the Committee Position of the person
     */
    private function committee_member() {
        $this->committee_member=$this->person ?? $this->person->committee_position;
    }

    /**
     * Get the Membership History of the person
     */
    private function membership_history() {
        $this->membership_history=$this->person->membership_records()->get();
    }

    /**
     * Is the current person a current member
     */
    private function isCurrentMember() {
        $membership_record=$this->person->membership_records()->where('year',$this->current_year)->count();
        $this->current_member=($membership_record > 0) ? 'true' : 'false';
    }

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        $view->with([
            'person'  => $this->person,
            'membership_history' => $this->membership_history,
            'current_member' => $this->current_member,
            'first_membership_record' => $this->person->is_member,
            'previous_membership_record' => $this->person->is_member_previous_year,
            'is_contact_only' => $this->person->is_contact_only,
            'year' => Utils::currentYear(),
        ]);
    }
}
