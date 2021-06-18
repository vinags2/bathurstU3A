<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Person;
use App\Helpers\Utils;

class EditMemberComposer
{
    private $person;
    private $postal_address = null;
    private $emergency_contact = null;
    private $payment_method = null;
    private $newsletter_delivery = null;
    private $venues = null;
    private $committee_member = null;
    private $currentYear;
    private $state;
    private $memberId;
    private $member;
    private $payment_received;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->saveMemberId();
        $this->currentYear = Utils::currentYear();
        $this->state       = $this->getState();
        $this->getPerson();
        $this->getPaymentReceived();
    }

    /**
     * If the memberId is a session variable (from using back(view)->with('memberId',...) in PersonController),
     * then save it as a request variable.
     * Otherwise, if user is a basic member, use the member's id,
     * Else get the memberId from the Request variable
     * If all else fails, set the memberId to -1.
     */
    private function saveMemberId() {
        if (session('memberId')) {
            request()->merge(['memberId'=>session('memberId')]);
        }
        if (auth()->user()->hasPermissionTo('basic member')) {
            $this->memberId = auth()->user()->person_id;
        } else {
          $this->memberId = request()->get('memberId',-1);
        }
    }

    /**
     * Is the memberId a valid integer?
     */
    private function isValidInteger() {
        return filter_var($this->memberId, FILTER_VALIDATE_INT) !== false;   
    }

    /**
     * Is the member ID an id of an existing person?
     */
    private function isValidPerson() {
        return !is_null(Person::find($this->memberId));
    }

    /**
     * Is the memberId valid?
     */
    private function isValidMemberId() {
        return $this->isValidInteger() && $this->isValidPerson();
    }

    /**
     * What state is the app in?
     * Possible states are: 'update existing member', 'new member', or 'member search'
     * Used in edit.blade.php (as well as here)
     */
     private function getState() {
         if (request()->filled('memberId') && $this->isValidMemberId()) {
             return 'update existing member';
         }
         if (request()->filled('newMember')) {
             return 'new member';
         }
         return 'member search';
     }

    /**
     * Initialize the Person
     */
    private function getPerson() {
        if ($this->memberId == -1) {
            $this->person = new Person;
            $this->person->id = -1;
            $this->person->first_name = ucfirst(strtolower(request()->input('first_name', null)));
            $this->person->last_name = ucfirst(strtolower(request()->input('last_name', null)));
            $this->member = 1;
            $this->addresses = null;
            $this->emergency_contact = null;
            $this->payment_method = null;
            $this->newsletter_delivery = null;
            $this->committee_member = null;
        } else {
            $this->person = Person::find($this->memberId);
            $this->addresses();
            $this->emergency_contact();
            $this->payment_method();
            $this->newsletter_delivery();
            $this->committee_member();
            $this->member = $this->person->is_member || $this->person->is_member_previous_year;
        }
        // If the user has clicked 'non-member' on the edit member form
        if (session()->has('_old_input.nonMember')) {
            $this->member = 0;
        }
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
     * Has the payment for membership been received?
     */
    private function getPaymentReceived() {
        $this->payment_received = $this->person->membership_records()->where('year',$this->currentYear)->first('payment_received');
    }

    /**
     * Open the View with the appropriate data passed
     */
    public function compose(View $view) {
        $view->with([
            'person'                        => $this->person,
            'postal_address'                => $this->postal_address,
            'newsletter_delivery'           => $this->newsletter_delivery,
            'committee'                     => $this->committee_member,
            'emergency_contact'             => $this->emergency_contact,
            'payment_method'                => $this->payment_method,
            'venues'                        => $this->venues,
            'showDetails'                   => ($this->person->first_name != ""),
            'state'                         => $this->state,
            'member'                        => $this->member,
            'payment_received'              => $this->payment_received,
            'currentYear'                   => Utils::currentYear(),
        ]);
    }
}
