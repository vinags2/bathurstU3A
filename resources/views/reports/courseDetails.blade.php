@extends('layouts.menu')
@section('content')

    <script src="{{ asset('dist/dataentry.js') }}"> </script>

<div class="container">
     @include('partials.commonUI.pageHeading', ['pageHeading' => 'Details for'])
    <table>
                <tr><td></td></tr>
               <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="single_name">Course:</label></td>
                    <td>
                        <input type="text" id="name_input" data-url="{{ url('coursesearch') }}" class="form-control @error('single_name') is-invalid @enderror" name="single_name" value="{{ $course->name }}"/>
                        <input type="hidden" id="name_url" value="{{ url('coursesearch') }}" name="single_name_url" />
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
                            <select id="name_matches"  size="10" style="display:none" name="courseId" class="form-control custom-select" onchange="this.form.submit()">
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
    
    <table class="table table-striped table-sm table-bordered table-fit" id="details">
        <tbody>
        <tr>
            <td>
                description:
            </td>
            <td>
                {{ $course->description }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Sessions</b>
            </td>
        </tr>
        @foreach ($sessions as $session)
            <tr>
                <td>{{ $session->name }}</td>
                <td>Time: {{ $session->day }} from {{ $session->start }} to {{ $session->end }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Venue: 
                    @include('partials.helpers.venue',['venue' => $venues[$loop->index]['name'], 'id' => $venues[$loop->index]['id']])
                </td>
            </tr>
            @if (!empty($facilitators[$loop->index]->name))
            <tr>
                <td></td>
                <td>Facilitator:
                    @include('partials.helpers.member',['id' => $facilitators[$loop->index]->id, 'member' => $facilitators[$loop->index]->name, 'reportId' => $memberDetailsReport])
                </td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td>Term length: {{ $terms[$loop->index] }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Roll: {{ $roll_types[$loop->index] }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Class size: mimimum: {{ $minimumSizes[$loop->index] ?? 'unspecified' }}, maximum: {{ $maximumSizes[$loop->index] ?? 'unspecified' }} </td>
            </tr>
            @cannot('basic member')
            @if (!empty($attendees[$loop->index]))
            <tr>
                <td></td>
                <td>
                    Participants:
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @foreach ($attendees[$loop->index] as $session_attendees)
                        @include('partials.helpers.member',['member' => (!empty($session_attendees) ? $session_attendees->name: '') , 'id' => $session_attendees->id])
                        <br>
                    @endforeach
                </td>
            </tr>
            @else
            <tr>
                <td></td>
                <td>
                    No participants
                </td>
            </tr>
            @endif
            @endcannot
        @endforeach
        </tbody>
    </table>
</div>
<br>
@endsection