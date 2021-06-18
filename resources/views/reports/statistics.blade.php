@extends('layouts.menu')
@section('content')

<div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Statistical Report'])
    <table class="table table-striped table-sm table-bordered table-fit">
        <tbody>
            <tr><td colspan="2"><b>Members</b></td></tr>
            <tr>
                <td> Number of members: </td>
                <td> {{ $members->numberOfMembers }} </td>
            </tr>
            <tr>
                <td> Most active members: </td>
                <td>
                    @foreach ($mostActiveMembers as $member)
                            @include('partials.helpers.member',['member' => $member->firstName.' '.$member->lastName, 'id' => $member->personId])<br>
                     @endforeach
                </td>
            </tr>
            <tr>
                <td> Members with no email address: </td>
                <td> {{ $members->noEmail }} </td>
            </tr>
            <tr>
                <td> Members with no phone: </td>
                <td> {{ $members->noPhone }} </td>
            </tr>
            <tr>
                <td> Number of committee members: </td>
                <td> {{ $members->numberOfCommitteeMembers }} </td>
            </tr>
            <tr><td colspan="2"><b>Newsletters</b></td></tr>
            <tr>
                <td> Delivered by email: </td>
                <td> {{ $members->newsletterByEmail }} </td>
            </tr>
            <tr>
                <td> - estimated postage savings p.a.: </td>
                <td> ${{ $members->newsletterByEmail*4 }} </td>
            </tr>
            <tr>
                <td> Delivered by post: </td>
                <td> {{ $members->newsletterByPost }} </td>
            </tr>
            <tr><td colspan="2"><b>Courses</b></td></tr>
            <tr>
                <td> Number of courses since 2017: </td>
                <td> {{ $totalCourses->totalCourses }} </td>
            </tr>
            <tr>
                <td> Number of current courses: </td>
                <td> {{ $courses->numberOfSessions }} </td>
            </tr>
            <tr>
                <td> Monday courses: </td>
                <td> {{ $courses->mondaySessions }} </td>
            </tr>
            <tr>
                <td> Tuesday courses: </td>
                <td> {{ $courses->tuesdaySessions }} </td>
            </tr>
            <tr>
                <td> Wednesday courses: </td>
                <td> {{ $courses->wednesdaySessions }} </td>
            </tr>
            <tr>
                <td> Thursday courses: </td>
                <td> {{ $courses->thursdaySessions }} </td>
            </tr>
            <tr>
                <td> Friday courses: </td>
                <td> {{ $courses->fridaySessions }} </td>
            </tr>
            <tr>
                <td> Morning courses: </td>
                <td> {{ $courses->morningSessions }} </td>
            </tr>
            <tr>
                <td> Afternoon courses: </td>
                <td> {{ $courses->afternoonSessions }} </td>
            </tr>
            <tr>
                <td> Most popular courses: </td>
                <td>
                    @foreach ($mostPopularCourses as $course)
                        @include('partials.helpers.course',['course' => $course->sessionName, 'id' => $course->courseId])
                            ({{ $course->quantity }} participants)<br>
                     @endforeach
                </td>
            </tr>
            <tr>
                <td> Least popular courses: </td>
                <td>
                    @foreach ($leastPopularCourses as $course)
                        @include('partials.helpers.course',['course' => $course->sessionName, 'id' => $course->courseId])
                            ({{ $course->quantity }} participants)<br>
                     @endforeach
                </td>
            </tr>
            <tr><td colspan="2"><b>Venues</b></td></tr>
            <tr>
                <td> Number of venues in use: </td>
                <td> {{ $courses->numberOfActiveVenues }} </td>
            </tr>
            <tr>
                <td> Most popular venues: </td>
                <td>
                    @foreach ($mostPopularVenues as $venue)
                        @include('partials.helpers.venue',['venue' => $venue->venueName, 'id' => $venue->venue_id])
                            ({{ $venue->quantity }} courses)<br>
                     @endforeach
                </td>
            </tr>
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