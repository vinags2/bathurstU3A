@extends('layouts.menu')
@section('content')

<div class="container">
    @include('partials.helpers.select',['name' => 'venueId','items' => $venues, 'selectedOption' => $venue->name])
    
    <table class="table table-striped table-sm table-bordered table-fit">
        <tbody>
        @if (!empty($address))
        <tr>
            <td>
                address:
            </td>
            <td>
                @include('partials.helpers.address',['address' => $address, 'includeSuburbAndPostcode' => true])
            </td>
        </tr>
        @endif
        @cannot('basic member')
        @if (!empty($contact->name))
        <tr>
            <td>
                contact:
            </td>
            <td>
                @include('partials.helpers.member',['member' => $contact->name, 'id' => $contact->id])
            </td>
        </tr>
        @endif
        @endcannot
        @if (!empty($sessions))
        <tr>
            <td> courses: </td>
                <td>
                    <table class="table table-borderless table-nostriped nobottommargin">
                    @foreach ($sessions as $session)
                        <tr>
                            <td class="tablerow-nopadding">
                                @include('partials.helpers.course',['course' => (!empty($session['name']) ? $session['name'] : '') , 'id' => $session['href']])
                            </td>
                            <td class="tablerow-nopadding">{{ $session['day of week'] }}</td>
                            <td class="tablerow-nopadding rightjustify">{{ $session['start time'] }}</td>
                            <td class="tablerow-nopadding"> - </td>
                            <td class="tablerow-nopadding">{{ $session['end time'] }}</td>
                        </tr>
                    @endforeach
                    </table>
                </td>
        </tr>
        @endif
        @if (!empty($oldSessions))
        <tr>
            <td> previous courses: </td>
                <td>
                    <table class="table table-borderless table-nostriped nobottommargin">
                    @foreach ($oldSessions as $session)
                        <tr>
                            <td class="tablerow-nopadding">{{ $session['name'] }}</td>
                            <td class="tablerow-nopadding">{{ $session['day of week'] }}</td>
                            <td class="tablerow-nopadding rightjustify">{{ $session['start time'] }}</td>
                            <td class="tablerow-nopadding"> - </td>
                            <td class="tablerow-nopadding">{{ $session['end time'] }}</td>
                        </tr>
                    @endforeach
                    </table>
                </td>
        </tr>
        @endif
        </tbody>
    </table>
</div>
<br>
@endsection