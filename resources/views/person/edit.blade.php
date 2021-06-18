@extends('layouts.menu')

@section('content')
<script src="{{ asset('dist/utilities.js') }}"> </script>
<script src="{{ asset('dist/ajax.js') }}"> </script>
<script src="{{ asset('dist/newmember.js') }}"> </script>

    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit, rejoin or new Member'])
        @if (session('success'))
            <div class="container">
                <table class="table-sm alert-success ml-5"><tr><td>
                    {{ session('success') }}
                </td></tr><tr><td></td></tr></table>
            </div>
        @elseif ($errors->any())
            <div class="container">
                <table class="table-sm alert-danger ml-5"><tr><td>
                    Please fix the errors below...
                </td></tr><tr><td></td>></tr></table>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br/>
                @endforeach
            </div>
        @endif
        @if (!$showDetails)
            @include('partials.search.person')
        @else
        <form id="mainForm" method="post" action="{{ route('person.store') }}">
            @csrf
            <table class="table-sm" id="details">
            <tr>
                <td  colspan="2" class="border">
                    @if ($person->is_member_next_year)
                        {{ $person->name }} is a financial member for {{ $currentYear + 1 }}.
                    @elseif ($person->is_member)
                        {{ $person->name }} is a financial member for $currentYear.
                    @elseif ($person->is_member_previous_year)
                        {{ $person->name }} is not a current financial member, but was a member in {{ $currentYear-1 }}.
                    @else
                        {{ $person->name }} is currently only a contact (eg for a venue, or as an emergency contact).
                    @endif
                </td>
            </tr>
                
                <tr><td id="nonMemberHeading" colspan="2"><b>
                    Person's details</b><td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="firstName">First Name:</label></td>
                    <td>
                        <input type="hidden" id="id" name="id" value="{{ old('id', $person['id']) }}"/>
                        <input type="text" size="40" id="firstName" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name',$person['first_name']) }}"
                        @if ($state == 'update existing member')
                            readonly="readonly"
                        @endif
                        />
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="lastName">Last Name:</label></td>
                    <td>
                        <input type="text" id="lastName" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $person['last_name']) }}"
                        @if ($state == 'update existing member')
                            readonly="readonly"
                        @endif
                        />
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        @php
                            $closeMatchingNames = request()->session()->pull('closeMatchingNames', null);
                        @endphp
                        @if (!empty($closeMatchingNames))
                            {{-- <label for="item" class="mt-1">Report for </label> --}}
                            <input type="hidden" id="closeMatchingNamesShown" name="closeMatchingNamesShown" value="true"/>
                            <select id="item" name="closeMatchingNames" class="form-control custom-select" size="3">
                                <option value="-1" disabled="disabled">If the name is in the list, please select it...</option>
                                @foreach ($closeMatchingNames as $name)
                                    <option value="{{ $name['id'] }}">{{ $name['name'] }}</option>
                                @endforeach
                            </select>
                            <br>
                            <input class="form-check-input ml-3"  id="confirm_name" type="checkbox" name="confirm_name" value="true">
                            <label class="form-check-label ml-5" for="confirm_name">accept the name as entered</label><BR>
                            <!-- <input class="form-check-input ml-3"  id="confirm_name" type="radio" name="confirm_name" value="false"> -->
                            <!-- <label class="form-check-label ml-5" for="confirm_name">re-enrol/edit the selected person</label><BR> -->
                        @endif
                        <span id="acceptorreenrolerror" style="display:none" class="invalid-feedback" role="alert">
                            <strong>You must make a choice: accept or re-enrol the member</strong>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="phone">Phone:</label></td>
                    <td>
                        <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $person['phone']) }}" autofocus="autofocus"/>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="mobile">Mobile:</label></td>
                    <td>
                        <input type="text" id="mobile" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile', $person['mobile']) }}"/>
                        @error('mobile')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="email">Email:</label></td>
                    <td>
                        <input type="text" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $person['email']) }}"/>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr><td></td><td></td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="line_1">Postal Address:</label></td>
                    <td>
                        <input type="text" id="address_input" class="form-control @error('line_1') is-invalid @enderror" name="line_1" value="{{ old('line_1', $postal_address['line_1']) }}"/>
                        <input type="hidden" id="address_url" value="{{ url('addresssearch') }}"/>
                        @error('line_1')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                            <select id="address_matches"  size="10" style="display:none" name="matches" class="form-control custom-select" onchange="displayNames(ajaxAddress)">
                            </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="text" id="line_2" class="form-control @error('line_2') is-invalid @enderror" name="line_2" value="{{ old('line_2', $postal_address['line_2']) }}"/>
                        @error('line_2')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="suburb">Suburb/Town/Locality:</label></td>
                    <td>
                        <input type="text" id="suburb" class="form-control @error('suburb') is-invalid @enderror" name="suburb" value="{{ old('suburb', $postal_address['suburb']) }}"/>
                        @error('suburb')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="postcode">Postcode:</label></td>
                    <td>
                        <input type="text" id="postcode" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode', $postal_address['postcode']) }}"/>
                        @error('postcode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>Emergency Contact details (eg next of kin)</b><td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_first_name">First Name:</label></td>
                    <td>
                        <input type="text" size="40" id="emergency_contact_first_name_input" class="form-control @error('emergency_contact_first_name') is-invalid @enderror" name="emergency_contact_first_name" value="{{ old('emergency_contact_first_name', $emergency_contact['first_name']) }}"/>
                        @error('emergency_contact_first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_last_name">Last Name:</label></td>
                    <td>
                        <input type="text" id="emergency_contact_last_name_input" data-url="{{ url('namesearch') }}" class="form-control @error('emergency_contact_name') is-invalid @enderror" name="emergency_contact_last_name" value="{{ old('emergency_contact_last_name', $emergency_contact['last_name']) }}"/>
                        <input type="hidden" id="name_url" value="{{ url('closenamesearch') }}" name="emergency_contact_url" />
                        @error('emergency_contact_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr id="emergency_contact_name_matches_row" class="nonMember matchingEmergencyContactNames">
                {{--<tr style="display:none" id="emergency_contact_name_matches_row" class="nonMember matchingEmergencyContactNames"> --}}
                    <td></td>
                    <td> 
                        {{--<select style="display:none" id="emergency_contact_name_matches"  size="10" name="memberId" class="form-control custom-select" onchange="displayEmergencyContactNames()" >--}}
                        <select style="display:none" id="emergency_contact_name_matches"  size="10" name="memberId" class="form-control custom-select" onchange="displayNames(ajaxEmergencyContact)" >
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input  id="emergency_contact_clear_names" type="checkbox" name="emergency_contact_clear_names" onChange="ajaxClearValues(ajaxEmergencyContact)" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="emergency_contact_clear_names">clear the name</label>
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_phone">Phone:</label></td>
                    <td>
                        <input type="text" id="emergency_contact_phone_input" class="form-control @error('emergency_contact_phone') is-invalid @enderror" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $emergency_contact['phone']) }}"/>
                        @error('emergency_contact_phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input  id="sameEmail" type="checkbox" size="40" name="sameEmail" onChange="copyValue(this, 'phone', 'emergency_contact_phone_input')" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="samePhoneNumber">same as member's phone number</label>
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_mobile">Mobile:</label></td>
                    <td>
                        <input type="text" id="emergency_contact_mobile_input" class="form-control @error('emergency_contact_mobile') is-invalid @enderror" name="emergency_contact_mobile" value="{{ old('emergency_contact_mobile', $emergency_contact['mobile']) }}"/>
                        @error('emergency_contact_mobile')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_email">Email:</label></td>
                    <td>
                        <input type="text" id="emergency_contact_email_input" class="form-control @error('emergency_contact_email') is-invalid @enderror" name="emergency_contact_email" value="{{ old('emergency_contact_email', $emergency_contact['email']) }}"/>
                        @error('emergency_contact_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input  id="sameEmail" type="checkbox" size="40" name="sameEmail" onChange="copyValue(this, 'email', 'emergency_contact_email_input')" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="sameEmail">same as member's email</label>
                    </td>
                </tr>
                <tr><td colspan="2"><b>Payment Details</b></td></tr>
                <tr><td colspan="2">The membership fee has been paid by:</td></tr>
                <tr><td colspan="2">
                    <input class="form-check-input ml-3 @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" value="1"
                        {{ $person['payment_method'] == '1' || old('payment_method') == '1' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="payment_method">leaving a cash or cheque in an envelope at BINC, or</label><BR>
                    <input class="form-check-input ml-3 @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" value="2"
                        {{ $person['payment_method'] == '2' || old('payment_method') == '2' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="payment_method">posting a cheque to The President, Bathurst U3A, PO Box 1332, Bathurst, 2795, or</label><BR>
                    <input class="form-check-input ml-3 @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" value="3"
                        {{ $person['payment_method'] == '3' || old('payment_method') == '3' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="payment_method">direct credit to the Reliance Bank, Bathurst</label><BR>
                    <input class="form-check-input ml-3 @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" value="4"
                        {{ $person['payment_method'] == '4' || old('payment_method') == '4' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="payment_method">no payment required (eg honorary member)</label><BR>
                        @error('payment_method')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    {{--
                    <SMALL>The Reliance Bank details are:</SMALL><BR>
                    <SMALL>BSB: 882-000, Account number: 300003108, Name: Bathurst U3A Inc, Reference: your name.</SMALL>
                    --}}
                </td></tr>
                <tr>
                    <!-- <td></td> -->
                    <td colspan="2">
                        <input  id="paymentReceived" type="checkbox" size="40" name="paymentReceived" class="form-check-input ml-3"
                        @if ($payment_received)
                            checked="checked"
                        @endif
                        >
                        <label class="form-check-label ml-5" for="paymentReceived">Payment has been received.</label>
                    </td>
                </tr>
                <tr><td colspan="2"><b>Newsletter delivery method</b></td></tr>
                <tr><td colspan="2">The quarterly newsletters are to be delivered by:</td></tr>
                <tr><td colspan="2">
                    <input class="form-check-input ml-3 @error('prefer_email') is-invalid @enderror" type="radio" name="prefer_email" value="1"
                        {{ $person['prefer_email'] == '1' || old('prefer_email') == '1' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="prefer_email">email, or</label><BR>
                    <input class="form-check-input ml-3 @error('prefer_email') is-invalid @enderror" type="radio" name="prefer_email" value="0"
                        {{ $person['prefer_email'] == '0' || old('prefer_email') == '0' ?  'checked' : '' }}>
                    <label class="form-check-label ml-5" for="prefer_email">post</label><BR>
                        @error('prefer_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    {{--
                    <SMALL>To keep our costs down, we would really appreciate if you elected to receive your newsletters by email.</SMALL>
                    --}}
                </td></tr>
                {{--
                <tr><td colspan="2"><b>Agreements</b></td></tr>
                <tr>
                    <td colspan="2">
                        <input  id="over50" type="checkbox" name="over50" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="over50">I am over 50 years of age. </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input  id="tac" type="checkbox" name="tac" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="tac">
                            I agree to comply with the <A HREF="http://bathurst.u3anet.org.au/?page_id=385" TARGET="_BLANK">
                            Bathurst U3A constitution</A> and <A HREF="http://bathurst.u3anet.org.au/?page_id=2113" TARGET="_BLANK">
                            official policies</A>
                        </label><br>
                        <label class="form-check-label ml-5" for="tac">
                            as published on the Bathurst U3A website.
                        </label>
                    </td>
                </tr>
                --}}
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="notes">Notes:</label></td>
                    <td>
                        <input type="text" maxlength="100" id="comment" class="form-control @error('notes') is-invalid @enderror" name="comment" value="{{ old('comment', $person['comment']) }}"/>
                        @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr><td></td><td>[Max characters = 100]</td></tr>
                <!-- <tr><td colspan="2"><b>Actions</b></td></tr> -->
                <tr>
                    <td colspan="2" class="pt-4">
                        @if ($person->is_member)
                            @if ($person->is_member_next_year)
                                <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                                <button type="submit" name="revoke" value="true" class="btn btn-primary btn-sm">Revoke</button>
                                <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                                <!-- <input class="form-check-input ml-3" type="radio" name="actions" value="7" checked>
                                <label class="form-check-label ml-5" for="actions">Save changes to the member's details</label><BR>
                                <input class="form-check-input ml-3" type="radio" name="actions" value="8">
                                <label class="form-check-label ml-5" for="actions">Cancel next year's membership</label><BR> -->
                            @else
                                <button type="submit" name="renew" value="true" class="btn btn-primary btn-sm">Renew</button>
                                <button type="submit" name="revoke" value="true" class="btn btn-primary btn-sm">Revoke</button>
                                <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                                <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                                <!-- <input class="form-check-input ml-3" type="radio" name="actions" value="1" checked>
                                <label class="form-check-label ml-5" for="actions">Renew membership for next year</label><BR>
                                <input class="form-check-input ml-3" type="radio" name="actions" value="2">
                                <label class="form-check-label ml-5" for="actions">Save changes to the member's details</label><BR>
                                <input class="form-check-input ml-3" type="radio" name="actions" value="3">
                                <label class="form-check-label ml-5" for="actions">Cancel the  membership</label><BR> -->
                            @endif
                        @elseif ($person->is_member_previous_year)
                            <button type="submit" name="renew" value="true" class="btn btn-primary btn-sm">Renew</button>
                            <button type="submit" name="revoke" value="true" class="btn btn-primary btn-sm">Revoke</button>
                            <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                            <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                            <!-- <input class="form-check-input ml-3" type="radio" name="actions" value="4" checked>
                            <label class="form-check-label ml-5" for="actions">Renew membership</label><BR>
                            <input class="form-check-input ml-3" type="radio" name="actions" value="2">
                            <label class="form-check-label ml-5" for="actions">Save changes to the member's details</label><BR>
                            <input class="form-check-input ml-3" type="radio" name="actions" value="3">
                            <label class="form-check-label ml-5" for="actions">Cancel the  membership</label><BR> -->
                        @else
                            <button type="submit" name="join" value="true" class="btn btn-primary btn-sm">Join</button>
                            <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                            <!-- <input class="form-check-input ml-3" type="radio" name="actions" value="5" checked>
                            <label class="form-check-label ml-5" for="actions">Join the Bathurst U3A</label><BR> -->
                        @endif
                    </td>
                </tr>
                <tr class="member"
                    <!-- @if ($member)
                       style="display:none"
                    @endif -->
                >
                    <td colspan="2">
                        <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                        <!-- <input class="form-check-input ml-3" type="radio" name="actions2" value="6" checked>
                        <label class="form-check-label ml-5" for="actions">Save the changes to the contact/non-member</label><BR> -->
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2" class="pt-4" >
                        <button type="submit" name="save" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                        <button type="reset" class="btn btn-primary btn-sm">Reset</button>
                    </td> -->
                <!-- </tr> -->
            </table>
            <input type="hidden" id="memberId" value="{{ $person->id }}" name="memberId" />
            <input type="hidden" id="state" value="{{ $state }}" name="state" />
        </form>
        @endif
    </div>
@endsection
