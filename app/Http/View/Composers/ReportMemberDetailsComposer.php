<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Person;
use App\Helpers\Utils;

class ReportMemberDetailsComposer
{
    private $memberId;
    private $user;
    private $residential_address;
    private $postal_address;
    private $partner;
    private $emergency_contact;
    private $payment_method;
    private $newsletter_delivery;
    private $membershipRecords;
    private $attendanceRecords;
    private $historicalAttendanceRecords;
    private $facilitatedClasses;
    private $venues;
    private $committee_member;
    private $members;
    private $alsoAtThisAddress;
    private $currentYear;

    public function __construct()
    {
        $this->initializeVariables();
    }

    private function initializeVariables() {
        $this->currentYear = Utils::currentYear();
        $this->initializeMembers();
        $this->addresses();
        $this->partner();
        $this->emergency_contact();
        $this->payment_method();
        $this->newsletter_delivery();
        $this->membership_records();
        $this->attendance_records();
        $this->historical_attendance_records();
        $this->facilitates();
        $this->contactFor();
        $this->committee_member();
        $this->alsoAtThisAddress();
        $this->comment();
    }

    /**
     * Initializw the memberId
     *  
     * If no member id is passed in the URL, use the logged in user instead
     */
    private function initializeMembers() {
        $user = auth()->user();
        if ($user->hasPermissionTo('basic member')) {
            $memberId = $user->person_id;
        } else {
            $memberId       = $this->memberId ?? request()->get('memberId',$user->person_id);
        }
        $this->memberId = (filter_var($memberId, FILTER_VALIDATE_INT) !== false) ? $memberId : auth()->user()->person_id;   
        $this->user     = Person::find($this->memberId);
        $this->members  = Person::orderBy('name')->get();
    }

    private function partner() {
        $this->partner = $this->user->partner()->first();
    }

    private function emergency_contact() {
        $this->emergency_contact = $this->user->emergency_contact()->first();
    }

    private function addresses() {
        $this->residential_address = $this->user->residential_address_details()->first();
        $this->postal_address      = $this->user->postal_address_details()->first();
    }

    private function payment_method() {
        $this->payment_method = $this->user->payment_method ?? 'not recorded';
        switch ($this->payment_method) {
            case 0: $this->payment_method = 'not paid';                 break;
            case 1: $this->payment_method = 'left cash/cheque at BINC'; break;
            case 2: $this->payment_method = 'posted a cheque';          break;
            case 3: $this->payment_method = 'direct credit';            break;
            case 4: $this->payment_method = 'non-standard method';      break;
            case 5: $this->payment_method = 'honorary member';          break;
        }
    }

    private function newsletter_delivery() {
        $this->newsletter_delivery = ($this->user->prefer_email == 1) ? "by email" : "by post";
    }

    private function membership_records() {
        $joinData                = $this->user->membership_records()->get();
        $this->membershipRecords = [];
        foreach ($joinData as $oneYear) {
            $this->membershipRecords[] = ['year' => $oneYear->year, 'date_of_admission' => date('d M Y',strtotime($oneYear->date_of_admission))];
        }
    }

    private function attendance_records() {
        // $classes                 = $this->user->attendance_records()->get();
        $classes                 = $this->user->attendance_records()
                                        ->where('year',$this->currentYear)->get();
        $this->attendanceRecords = [];
        foreach ($classes as $oneYear) {
            $session                   = $oneYear->session()->first();
            $course                    = $session->course()->first();
            // $this->CurrentAttendanceRecords[] = ['session_name' => $session->name, 'date_of_enrolment' => $oneYear->date_of_enrolment, 'course id' => $course->id];
            $this->attendanceRecords[] = ['session_name' => $session->name, 'date_of_enrolment' => $oneYear->date_of_enrolment, 'course id' => $course->id];
        }
    }

    private function historical_attendance_records() {
        $classes                 = $this->user->attendance_records()
                                        ->where('year','!=',$this->currentYear)
                                        ->orderBy('year')
                                        ->get();
        $this->historicalAttendanceRecords = [];
        foreach ($classes as $oneYear) {
            $this->historicalAttendanceRecords[] = ['year' => $oneYear->year,'session_name' => $oneYear->session()->first()->name, 'date_of_enrolment' => $oneYear->date_of_enrolment];
            }
    }

    private function facilitates() {
        // TODO: make a scope for sessions not deleted or suspended
        $facilitates              = $this->user->facilitates()->where('deleted',0)->where('suspended',0)->get();
        $this->facilitatedClasses = [];
        foreach ($facilitates as $facilitate) {
            $this->facilitatedClasses[] = ['name' => $facilitate->name, 'id' => $facilitate->course_id];
        }
    }

    private function contactFor() {
        $contactFor   = $this->user->venues()->get();
        $this->venues = [];
        foreach ($contactFor as $venue) {
            $this->venues[] = ['name' => $venue->name, 'href' => $venue->id];
        }
    }

    private function alsoAtThisAddress() {
        $address   = $this->user->residential_address;
        $this->alsoAtThisAddress = [];
        if ($address) {
            foreach ($this->members as $member) {
                if (($member->residential_address == $address) and ($member->id <> $this->memberId)) {
                    $this->alsoAtThisAddress[] = ['name' => $member->name,'id' =>  $member->id];
                }
            }
        }
    }

    private function committee_member() {
        $this->committee_member=$this->user->committee_position;
    }

    private function comment() {
        $this->comment=$this->user->comment;
    }

    public function compose(View $view) {
        // dd($this->alsoAtThisAddress);
        $view->with([
            'user'                          => $this->user,
            'residential_address'           => $this->residential_address,
            'postal_address'                => $this->postal_address,
            'partner'                       => $this->partner,
            'newsletter_delivery'           => $this->newsletter_delivery,
            'membership_history'            => $this->membershipRecords,
            'attendance_records'            => $this->attendanceRecords,
            'historical_attendance_records' => $this->historicalAttendanceRecords,
            'committee'                     => $this->committee_member,
            'emergency_contact'             => $this->emergency_contact,
            'payment_method'                => $this->payment_method,
            'facilitates'                   => $this->facilitatedClasses,
            'venues'                        => $this->venues,
            'alsoAtThisAddress'             => $this->alsoAtThisAddress,
            'comment'                       => $this->comment
        ]);
    }
}
