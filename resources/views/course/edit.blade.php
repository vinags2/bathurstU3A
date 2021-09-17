@extends('layouts.menu')

@section('content')
<script src="{{ asset('dist/utilities.js') }}"> </script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script>

    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit, rejoin or new Course'])
        @include('partials.commonUI.showSuccessOrErrors')
        @if (!$showDetails)
            @include('partials.search.singleentry', [
                'modelName'   => $course->name,
                'model'       => 'course',
                'searchUrl'   => $searchUrl,
                'allNewModel' => $allowNewModel,
                'identifier'  => 'course1'
            ])
        @else
        <form id="mainForm" method="post" action="{{ route('course.store') }}">
            @csrf
            <table class="table-sm" id="details">
                <tr><td id="nonCourseHeading" colspan="2"><b>
                    Course's details</b><td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="courseName">Name:</label></td>
                    <td  colspan="2">
                        <input type="hidden" id="id" name="id" value="{{ old('id', $course['id']) }}"/>
                        <input type="text" size="40" id="courseName" class="form-control @error('coursename') is-invalid @enderror" name="course_name" value="{{ old('course_name',$course['name']) }}"
                        />
                        @error('course_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="align-top"><label class="col-xs-3 col-form-label mr-2" for="courseDescription">Description:</label></td>
                    <td  colspan="2">
                        <textarea rows="7" id="courseDescription" class="form-control @error('courseDescription') is-invalid @enderror"
                            name="course_description">{{ old('course_description',$course['description']) }}</textarea>
                        @error('course_description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="courseComment">Notes:</label></td>
                    <td  colspan="2">
                        <input type="text" size="40" maxlength="100" id="courseComment" class="form-control @error('coursecomment') is-invalid @enderror" name="course_comment" value="{{ old('course_comment',$course['comment']) }}"
                        />
                        @error('course_comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="courseComment">Suspended:</label></td>
                    <td  colspan="2">
                        <input type="checkbox" id="courseSuspended" class="form-control @error('coursesuspended') is-invalid @enderror" name="course_suspended"
                        @if (old('course_suspended',$course['suspended'])  == 1)
                            checked="checked"
                        @endif
                        />
                        @error('course_suspended')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="align-top"><label class="col-xs-3 col-form-label mr-2" for="courseComment">Effective from:</label></td>
                    <td>
                    <input class="form-check-input ml-1 @error('effective_from') is-invalid @enderror" type="radio" name="effective_from" value="nextTerm"
                        {{ old('effective_from', $effectiveFrom) == 'nextTerm' ?  'checked' : '' }}>
                    <label class="form-check-label ml-4" for="effective_from">next term</label><BR>
                    <input class="form-check-input ml-1 @error('effective_from') is-invalid @enderror" type="radio" name="effective_from" value="nextYear"
                        {{ old('effective_from', $effectiveFrom) == 'nextYear' ?  'checked' : '' }}>
                    <label class="form-check-label ml-4" for="effective_from">next year</label><BR>
                    <input class="form-check-input ml-1 @error('effective_from') is-invalid @enderror" type="radio" name="effective_from" value="immediately"
                        {{ old('effective_from', $effectiveFrom) == 'immediately' ?  'checked' : '' }}>
                    <label class="form-check-label ml-4" for="effective_from">immediately</label><BR>
                    <input class="form-check-input ml-1 @error('effective_from') is-invalid @enderror" type="radio" name="effective_from" value="other"
                        {{ old('effective_from', $effectiveFrom) == 'other' ?  'checked' : '' }}>
                    <label class="form-check-label ml-4" for="effective_from">other</label>
                    </td>
                    <td class="align-bottom">
                    <input type="date" size="20" id="effective_from_date" class="form-control @error('effectiveFromDate') is-invalid @enderror" name="effective_from_date" value="{{ old('effective_from_date',date('Y-m-d', strtotime($effectiveFromDate))) }}">
                    </td>
                </tr>
            </table>
            <table class="table-sm" id="sessionsTable">
                @foreach ($sessions as $session)
                        @php
                            $thisSession = 'sessions'.($loop->index+1);
                            $thisSessionId = 'sessionId'.($loop->index+1);
                        @endphp
                    <tr v-if="session[{{$loop->index}}]">
                        <td><b>Session {{ $loop->index+1 }}:</b></td>
                    </tr>
                    <tr v-if="session[{{$loop->index}}]">
                        <td><label class="col-xs-3 col-form-label mr-2" for="{{ $thisSession }}">Name:</label></td>
                        <td><input type="text" size="40" id="{{ $thisSession }}" class="form-control" name="{{ $thisSession }}" value="{{ old($thisSession, $session->name) }}"></td>
                        <input type="hidden" id="{{ $thisSessionId }}" name="{{ $thisSessionId }}" value="{{ $session->id }}"/>
                    </tr>
            @include('partials.search.singleentry', [
                'modelName'   => $course->name,
                'model'       => 'course',
                'searchUrl'   => $searchUrl,
                'allNewModel' => false,
                'identifier'  => $thisSession
            ])
                @endforeach
                <tr>
                    <td colspan="2" class="pt-4" >
                        <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
                        <button type="submit" name="save" class="btn btn-primary btn-sm" value="true">Save</button>
                        <button type="reset" class="btn btn-primary btn-sm">Reset</button>
                    </td>
                </tr>
            </table>
            <table class="table-sm" id="buttonsTable">
                <tr>
                    <td colspan="2">
                        <button type="submit" name="join" value="true" class="btn btn-primary btn-sm">Next</button>
                        <button type="button" onclick=cancelForm("{{ route('course.edit') }}") class="btn btn-primary btn-sm">Back</button>
                    </td>
                </tr>
            </table>
            <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
            <input type="hidden" id="memberId" value="{{ $course->id }}" name="memberId" />
            <input type="hidden" id="state" value="{{ $state }}" name="state" />
        </form>
        <script>
            var app2 = new Vue({
            el: '#sessionsTable',
            data: {
                session: [],
            },
            created: function () {
                for (i=0; i < 6; i++) { 
                    if (i < 2) {
                        this.session[i] = true
                    } else {
                        this.session[i] = false
                    }
                }
            },
            })
        </script>
        @endif
    </div>
@endsection
