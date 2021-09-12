<?php

namespace App\Http\Controllers;

use App\Person;
use App\Address;
use Illuminate\Http\Request;
use App\Rules\CloseMatchingNames;
use App\Helpers\Utils;
use App\MembershipHistory;
use App\Traits\Allowable;

class PersonController extends Controller
{
    use Allowable;
    /**
     * custom error messages when incorrect data is entered
     */
    private $messages = [
            'last_name.unique'                 => 'This person is already a member.',
            'required_with'                    => 'The :attribute is required.',
            'line_1.required_with'             => 'The address is required.',
            'emergency_contact_name.different' => 'The emergency contact cannot be the same person.',
        ];

    private $membershipHistoryMessages = [
        'last_year_of_membership' => 'Renewals are only accepted for this or next year'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return (new GenericReportController(6))->show();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('person.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if ($request->filled('contactDetailsOnly')) {
            $request            = $this->prepareDataForStorage($request);
            $validatedData      = $request->validate($this->rules($request), $this->messages);
            $addressId          = $this->validateAndCreateAddress($validatedData);
            $person             = Person::myUpdateOrCreateContactDetailsOnly($this->personData($validatedData),$this->extraPersonDataContactDetailsOnly($validatedData,$addressId));
            $this->saveMemberId($person);
        } else {
            $request            = $this->prepareDataForStorage($request);
            $validatedData      = $request->validate($this->rules($request), $this->messages);
            $addressId          = $this->validateAndCreateAddress($validatedData);
            $emergencyContactId = $this->validateAndCreateEmergencyContact($validatedData);
            // Check for valid year for membership record data <------ *******
            // validate the year, so that it returns an error if year is two or more years ahead.
            $person             = Person::myUpdateOrCreate($this->personData($validatedData),$this->extraPersonData($validatedData,$addressId,$emergencyContactId));
            $this->validateMembershipHistory($request, $person);
            $membershipHistoryId = MembershipHistory::myUpdateOrCreate($person->id);
            // dd($person->membership_records()->latest('year')->first(), request()->all());
            $this->saveMemberId($person);
        }
        return back()
            ->with('success', $person->first_name.' '.$person->last_name.' has been added or updated successfully')
            ->with('memberId',$person->id)
            ->withInput();
    }

    /**
     * Massage the data entered by the user into an acceptable format
     */
    private function prepareDataForStorage(Request $request) {
        $request = $this->stripSpacesFromPhoneNumbers($request);
        $request = $this->existingOrNewMember($request);
        $request = $this->concatenateNames($request);
        return $request;
    }

    /**
     * Validate the address data, and if a new address, create a new address
     */
    private function validateAndCreateAddress($validatedData) {
        $addressData = $this->addressData($validatedData);
        return (Address::myFirstOrCreate($addressData))->id;
    }

    /**
     * Validate the Membership History data, that is, check that if renewing, the renewal is not for two years time.
     */
    private function validateMembershipHistory($request, $person) {
        if (!$request->filled('renew')) {
            return;
        }
        $last_year_of_membership = $person->membership_records()->latest('year')->first() ?? Utils::currentYear();
        dd($last_year_of_membership);
        $request->merge(['last_year_of_membership' => $last_year_of_membership]);
        $request->validate(['last_year_of_membership' => 'lte:'.Utils::currentYear()], $this->membershipHistoryMessages);
    }
    
    /**
     * Validate the Emergency Contact data, and if a new Emergency Contact, create a new Person as a non-member
     */
    private function validateAndCreateEmergencyContact($validatedData) {
        $emergencyContactData = $this->emergencyContactData($validatedData);
        $emergencyContact     = Person::myUpdateOrcreate($emergencyContactData,$this->extraEmergencyContactData($validatedData));
        if ($emergencyContact->wasRecentlyCreated) {
            $emergencyContact->member = 0;
            $emergencyContact->save();
        }
        return $emergencyContact->id;
    }

    /**
     * pass the memberId to the blade file.
     */
    private function saveMemberId(Person $person) {
        session(['memberId'=>$person->id]);
    }

    /**
     * validation rules
     */
    private function rules($request) {
        $rules = [
            'phone'       => 'numeric|digits_between:8,10|nullable',
            'mobile'      => 'numeric|digits:10|nullable',
            'email'       => 'email|nullable',
            // address
            'line_1'      => 'nullable',
            'line_2'      => 'nullable',
            'suburb'      => 'required_with:line_1,line_2|nullable',
            'postcode'    => 'numeric|digits:4|required_with:line_1,line_2|nullable',
            'comment'     => 'nullable' ];

        if (!$request->filled('contactDetailsOnly')) {
            if (!$request->filled('nonMember')) {
                // emergency contact
                $rules['emergency_contact_name']        = 'nullable';
                $rules['emergency_contact_last_name']   = 'nullable';
                $rules['emergency_contact_first_name']  = 'nullable';
                $rules['emergency_contact_phone']       = 'nullable';
                $rules['emergency_contact_mobile']      = 'nullable';
                $rules['emergency_contact_email']       = 'nullable';
            } else {
                // emergency contact
                $rules['emergency_contact_name']        = 'different:member_name|nullable';
                $rules['emergency_contact_last_name']   = 'max:20|nullable|required_with:emergency_contact_first_name';
                $rules['emergency_contact_first_name']  = 'max:20|nullable|required_with:emergency_contact_last_name';
                $rules['emergency_contact_phone']       = 'numeric|digits_between:8,10|nullable';
                $rules['emergency_contact_mobile']      = 'numeric|digits:10|nullable';
                $rules['emergency_contact_email']       = 'email|nullable';
                // other information
                $rules['payment_method']                = 'required';
                $rules['prefer_email']                  = 'required';
                $rules['payment_received']              = 'nullable';
            }
        }

        if ($request->state == 'new member') {
            $rules['first_name']  = 'required|max:20';
            $rules['last_name']   = ['bail','required','max:20','unique:people,last_name,NULL,id,first_name,'.$request->first_name,
                new CloseMatchingNames($request->first_name, $request->confirm_name)];
        }
        if ($request->state == 'update existing member') {
            $rules['first_name']  = 'required|max:20';
            $rules['last_name']   = ['required','max:20'];
        }

        return $rules;
    }

    /**
     * Collect the address data into an array in preparation for creating a new address.
     */
    private function addressData($data) {
        $addressDataArray = [];
        if (!empty($data['line_1'])) {
            $addressDataArray = array_merge_recursive($addressDataArray,['line_1' => $data['line_1']]);
        }
        if (!empty($data['line_2'])) {
            $addressDataArray = array_merge_recursive($addressDataArray,['line_2' => $data['line_2']]);
        }
        $addressDataArray = array_merge_recursive(
            $addressDataArray,
            ['suburb'   => $data['suburb']],
            ['postcode' => $data['postcode']]
        );
        return $addressDataArray;
    }

    /**
     * Collect the Emergency Contact Key data into an array in preparation for creating a new Person as a non-member.
     */
    private function emergencyContactData($data) {
        $emergencyContactDataArray = [
            'first_name' => $data['emergency_contact_first_name'],
            'last_name'  => $data['emergency_contact_last_name'],
        ];
        return $emergencyContactDataArray;
    }

    /**
     * Collect the Emergency Contact non-key data into an array in preparation for creating a new Person as a non-member.
     */
    private function extraEmergencyContactData($data) {
        $emergencyContactDataArray = [];
        if (!empty($data['emergency_contact_phone'])) {
            $emergencyContactDataArray = array_merge_recursive($emergencyContactDataArray,['phone' => $data['emergency_contact_phone']]);
        }
        if (!empty($data['emergency_contact_mobile'])) {
            $emergencyContactDataArray = array_merge_recursive($emergencyContactDataArray,['mobile' => $data['emergency_contact_mobile']]);
        }
        if (!empty($data['emergency_contact_email'])) {
            $emergencyContactDataArray = array_merge_recursive($emergencyContactDataArray,['email' => $data['emergency_contact_email']]);
        }
        return $emergencyContactDataArray;
    }

    /**
     * Collect the Person Key data into an array in preparation for creating a new Person.
     */
    private function personData($data) {
        $personDataArray = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
        ];
        return $personDataArray;
    }

    /**
     * Collect the Person non-key data into an array in preparation for creating a new Person.
     */
    private function extraPersonData($data, $addressId, $emergencyContactId) {
        $personDataArray = [
            'payment_method' => $data['payment_method'] ??  1,
            'prefer_email'   => $data['prefer_email']   ??  0,
        ];
        if (!empty($data['phone'])) {
            $personDataArray = array_merge_recursive($personDataArray,['phone' => $data['phone']]);
        }
        if (!empty($data['mobile'])) {
            $personDataArray = array_merge_recursive($personDataArray,['mobile' => $data['mobile']]);
        }
        if (!empty($data['email'])) {
            $personDataArray = array_merge_recursive($personDataArray,['email' => $data['email']]);
        }
        if (!empty($data['comment'])) {
            $personDataArray = array_merge_recursive($personDataArray,['comment' => $data['comment']]);
        }
        if (!empty($addressId)) {
            $personDataArray = array_merge_recursive($personDataArray,['postal_address' => $addressId]);
        }
        if (!empty($emergencyContactId)) {
            $personDataArray = array_merge_recursive($personDataArray,['emergency_contact' => $emergencyContactId]);
        }
        // if (request()->filled('nonMember')) {
        //     $personDataArray = array_merge_recursive($personDataArray,['member' => 0]);
        // } else {
        //     $personDataArray = array_merge_recursive($personDataArray,['member' => 1]);
        // }

        return $personDataArray;
    }

    /**
     * Collect the Person non-key data into an array in preparation for creating a new Person.
     */
    private function extraPersonDataContactDetailsOnly($data, $addressId) {
        $personDataArray = [];
        if (!empty($data['phone'])) {
            $personDataArray = array_merge_recursive($personDataArray,['phone' => $data['phone']]);
        }
        if (!empty($data['mobile'])) {
            $personDataArray = array_merge_recursive($personDataArray,['mobile' => $data['mobile']]);
        }
        if (!empty($data['email'])) {
            $personDataArray = array_merge_recursive($personDataArray,['email' => $data['email']]);
        }
        if (!empty($data['comment'])) {
            $personDataArray = array_merge_recursive($personDataArray,['comment' => $data['comment']]);
        }
        if (!empty($addressId)) {
            $personDataArray = array_merge_recursive($personDataArray,['postal_address' => $addressId]);
        }
        // if (request()->filled('nonMember')) {
        //     $personDataArray = array_merge_recursive($personDataArray,['member' => 0]);
        // } else {
        //     $personDataArray = array_merge_recursive($personDataArray,['member' => 1]);
        // }

        return $personDataArray;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        return view('reports.memberDetails');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        // return view('person.edit');
        return static::userAllowable('person.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function editContactDetails(Person $person)
    {
        return view('person.editContactDetails');
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }

    /**
     * Return a json of close-matching and exact names
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $first_name = $request->input('first_name');
        $last_name  = $request->input('last_name');
        $members = \App\Person::closeAndExactMatchingNames($first_name, $last_name)
            ->select('id', 'name', 'first_name', 'last_name', 'phone', 'mobile', 'email')
            ->limit(10)
            ->orderBy('last_name')
            ->get();
        return response()->json($members);
    }
 
    /**
     * Return a json of close-matching names
     *
     * @return \Illuminate\Http\Response
     */
    public function closesearch(Request $request)
    {
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $members = Person::select('id','name', 'first_name', 'last_name', 'phone', 'mobile', 'email')
            ->where('first_name','like','%'.$first_name.'%')
            ->where('last_name','like','%'.$last_name.'%')
            ->limit(10)
            ->orderBy('last_name')
            ->get();
        return response()->json($members);
    }

    /**
     * Remove all spaces from phone numbers
     */
    private function stripSpacesFromPhoneNumbers(Request $request) {
        $mobile = str_replace(" ", "", $request->get('mobile'));
        $request->merge(['mobile' => $mobile]);
        $phone = str_replace(" ", "", $request->get('phone'));
        $request->merge(['phone' => $phone]);
        return $request;
    }

    /**
     * Check to make sure that when a user has entered a name similar to an existing name, that it is not a typo.
     * The user has clicked a checkbox to indicate that the similar name is intended.
     */
    private function existingOrNewMember(Request $request) {
        if ($request->session()->has('accepted name')) {
            return $request;
        }
        if (!$request->filled('confirm_name')) {
            return $request;
        }
        if ($request->get('confirm_name', 'dummy') == 'true') {
            $request->session()->put('accepted name', 'yes');
            return $request;
        }
        return redirect('person')->withInput();
    }

    /**
     * Concatenate first and last names, to be used in a success message to the user.
     */
    private function concatenateNames(Request $request) {
        $request->merge(['member_name' => $request->get('first_name').' '.$request->get('last_name')]);
        $request->merge(['emergency_contact_name' => $request->get('emergency_contact_first_name').' '.$request->get('emergency_contact_last_name')]);
        return $request;
    }
}
