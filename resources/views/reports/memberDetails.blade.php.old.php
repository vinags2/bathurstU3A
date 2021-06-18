@extends('layouts.menu')
@section('content')

<div class="container">
    @include('partials.helpers.select',['name' => 'memberId','items' => $members, 'selectedOption' => $user->name])
    
    <table class="table table-striped table-sm table-bordered table-fit">
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