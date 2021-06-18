@extends('layouts.menu')
@section('content')

    <script src="{{ asset('dist/dataentry.js') }}"> </script>

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Details for'])
    <table>
                <tr><td></td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="emergency_contact_first_name">First Name:</label></td>
                    <td>
                        <input type="text" size="40" id="first_name_input" class="form-control @error('emergency_contact_first_name') is-invalid @enderror" name="first_name" value="{{ $user->first_name }}"
                        @can('basic member')
                            readonly
                        @endcan
                        />
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
                        <input type="text" id="last_name_input" data-url="{{ url('namesearch') }}" class="form-control @error('emergency_contact_last_name') is-invalid @enderror" name="last_name" value="{{ $user->last_name }}"
                        @can('basic member')
                            readonly
                        @endcan
                        />
                        <input type="hidden" id="name_url" value="{{ url('namesearch') }}" name="emergency_contact_url" />
                        @error('emergency_contact_last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                @canany(['edit users', 'admin', 'data entry'])
                <tr>
                    <td></td>
                    <td> 
                        <form action="" autocomplete="off" method="GET">
                            <select id="name_matches"  size="10" style="display:none" name="memberId" class="form-control custom-select" onchange="this.form.submit()">
                            </select>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td></td>
                        <td>
                            <input  id="clearNames" type="checkbox" name="clearNames" onChange="clearValues(this, 'first_name_input', 'last_name_input')" class="form-check-input ml-3">
                            <label class="form-check-label ml-5" for="samePhoneNumber">clear</label>
                        </td>
                </tr>
                @endcanany
                <tr><td>&nbsp;</td></tr>
    </table>
    
    <table class="table table-striped table-sm table-bordered table-fit" id="details">
        <tbody>
        @if (!empty($residential_address))
        <tr>
            <td>
                residential address:
            </td>
            <td>
                @include('partials.helpers.address',['address' => $residential_address, 'includeSuburbAndPostcode' => true])
            </td>
        </tr>
        @endif
        @if (!empty($alsoAtThisAddress))
        <tr>
            <td>
                also living at this address:
            </td>
            <td>
                @foreach ($alsoAtThisAddress as $member)
                    @include('partials.helpers.member',['member' => $member['name'], 'id' => $member['id']])<br>
                @endforeach
            </td>
        </tr>
        @endif
        @if (!empty($postal_address))
        <tr>
            <td>
                postal address:
            </td>
            <td>
                @include('partials.helpers.address',['address' => $postal_address, 'includeSuburbAndPostcode' => true, 'noLink' => true])
            </td>
        </tr>
        @endif
        @if (!empty($user->phone))
        <tr>
            <td>
                phone:
            </td>
            <td>
                {{ $user->phone }}
            </td>
        </tr>
        @endif
        @if (!empty($user->mobile))
        <tr>
            <td>
                mobile:
            </td>
            <td>
                {{ $user->mobile }}
            </td>
        </tr>
        @endif
        @if (!empty($user->email))
        <tr>
            <td>
                email:
            </td>
            <td>
                @include('partials.helpers.email',['email' => $user->email])
            </td>
        </tr>
        @endif
        <tr>
            <td>
                newsletter delivery:
            </td>
            <td>
                {{ $newsletter_delivery }}
            </td>
        </tr>
        <tr>
            <td>
                member:
            </td>
            <td>
                @if ($user->member)
                    Yes
                @else
                    No
                @endif
            </td>
        </tr>
        @if (!empty($committee))
        <tr>
            <td>
                committee member:
            </td>
            <td>
                {{ $committee }}
            </td>
        </tr>
        @endif
        @if (!empty($emergency_contact->name))
        <tr>
            <td>
                emergency contact:
            </td>
            <td>
                @include('partials.helpers.member',['member' => $emergency_contact->name, 'id' => $emergency_contact->id])
            </td>
        </tr>
        @endif
        <tr>
            <td>
                payment method:
            </td>
            <td>
                {{ !empty($payment_method) ? $payment_method: '' }}
            </td>
        </tr>
        @if (!empty($membership_history))
        <tr>
            <td>
                membership history:
            </td>
            <td>
                @foreach ($membership_history as $oneYear)
                    {{ $oneYear['year'] }} <small>(joined on {{ $oneYear['date_of_admission'] }})</small><br>
                @endforeach
            </td>
        </tr>
        @endif
        @if (!empty($facilitates))
        <tr>
            <td>
                facilitator for:
            </td>
            <td>
                @foreach ($facilitates as $facilitate)
                    @include('partials.helpers.course',['course' => (!empty($facilitate['name']) ? $facilitate['name'] : '') , 'id' => $facilitate['id']])<br>
                @endforeach
            </td>
        </tr>
        @endif
        @if (!empty($venues))
        <tr>
            <td>
                venue contact for:
            </td>
            <td>
                @foreach ($venues as $venue)
                    @include('partials.helpers.venue',['venue' => (!empty($venue['name']) ? $venue['name'] : '') , 'id' => $venue['href']])<br>
                @endforeach
            </td>
        </tr>
        @endif
        @if (!empty($attendance_records))
        <tr>
            <td>
                current classes:
            </td>
            <td>
                @foreach ($attendance_records as $attendance_record)
                    @include('partials.helpers.course',['course' => $attendance_record['session_name'], 'id' => $attendance_record['course id']])
                    <br>
                @endforeach
            </td>
        </tr>
        @endif
        @if (!empty($historical_attendance_records))
        <tr>
            <td>
                class history:
            </td>
            <td>
                @foreach ($historical_attendance_records as $historical_attendance_record)
                    {{ $historical_attendance_record['year'] }}: {{ $historical_attendance_record['session_name'] }}
                    @if (!empty($historical_attendance_record['date_of_enrolment']))
                        <small> (joined on {{ $historical_attendance_record['date_of_enrolment'] }})</small>
                    @endif
                    <br>
                @endforeach
            </td>
        </tr>
        @endif
        @canany(['edit users', 'admin', 'data entry'])
        @if (!empty($comment))
        <tr>
            <td>
                notes:
            </td>
            <td>
                {{ $comment }}
            </td>
        </tr>
        @endif
        @endcanany
        </tbody>
    </table>
    <small>Note:<ul>
        <li>there are no records prior to 2017</li>
        <li>the membership history dates for 2017-9 are the dates of recording in the database</li>
        <li>the historical data for 2017-9 is not 100% accurate, but is a pretty good guide</li>
    </ul></small>
</div>
<br>
@endsection