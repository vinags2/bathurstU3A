@extends('layouts.menu')
@section('content')

    <script src="{{ asset('dist/dataentry.js') }}"> </script>

<div class="container">
     @include('partials.commonUI.pageHeading', ['pageHeading' => 'Details for'])
    <table>
                <tr><td></td></tr>
               <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="single_name">Venue:</label></td>
                    <td>
                        <input type="text" autocomplete="false" id="name_input" data-url="{{ url('venuesearch') }}" class="form-control @error('single_name') is-invalid @enderror" name="single_name" value="{{ $venue->name }}"/>
                        <input type="hidden" id="name_url" value="{{ url('venuesearch') }}" name="single_name_url" />
                        @error('single_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td> 
                        <form action="" autocomplete="off" method="GET">
                            <select id="name_matches"  size="10" style="display:none" name="venueId" class="form-control custom-select" onchange="this.form.submit()">
                            </select>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td></td>
                        <td>
                            <input  id="clearNames" type="checkbox" name="clearNames" onChange="clearValues(this, 'name_input')" class="form-check-input ml-3">
                            <label class="form-check-label ml-5" for="samePhoneNumber">clear</label>
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
    </table>
    
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