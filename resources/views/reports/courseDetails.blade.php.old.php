@extends('layouts.menu')
@section('content')

<div class="container">
    @include('partials.helpers.select',['name' => 'courseId','items' => $courses, 'selectedOption' => $course->name])
    
    <table class="table table-striped table-sm table-bordered table-fit">
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