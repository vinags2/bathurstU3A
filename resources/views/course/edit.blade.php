@extends('layouts.menu')

@section('content')
<script src="{{ asset('dist/utilities.js') }}"> </script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script>
<script src = "{{ asset('js/singleentry.js') }}"></script>
<script src = "{{ asset('js/SingleEntryWithDbLookup.js') }}"></script>
<script src = "{{ asset('js/subheading.js') }}"></script>
<script src = "{{ asset('js/TextInputWithLabel.js') }}"></script>
<script src = "{{ asset('js/TextAreaWithLabel.js') }}"></script>
<script src = "{{ asset('js/CheckboxWithLabel.js') }}"></script>
<script src = "{{ asset('js/RadioButtonWithLabel.js') }}"></script>
<script src = "{{ asset('js/RadioButtonsWithLabels.js') }}"></script>
<script src = "{{ asset('js/RadioButtonWithDateInput.js') }}"></script>
<script src = "{{ asset('js/DropdownWithLabel.js') }}"></script>
<script src = "{{ asset('js/HorizontalRadioButtonsWithLabels.js') }}"></script>
<script src = "{{ asset('js/HorizontalInputsWithLabels.js') }}"></script>

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit, rejoin or new Course'])
    @include('partials.commonUI.showSuccessOrErrors')
    @if (!$showDetails)
        <div id = "course">
            <singleentry model="course" ajaxurl='{{ $searchUrl }}'></singleentry>
        </div>
        <script>
            var vm = new Vue({
                el: '#course'
            });
        </script>
    @else
        <form id="mainForm" method="post" action="{{ route('course.store') }}">
            @csrf
            <div id="details" >
                <subheading
                    subheading="Course's details" :width="5" >
                </subheading>
                <text-input-with-label
                    name="course_name"
                    value="{{ old('course_name',$course['name']) }}"
                    set-focus >
                </text-input-with-label>
                <text-area-with-label
                    name="course_description"
                    value="{{ old('course_description',$course['description']) }}" >
                </text-area-with-label>
                <text-input-with-label
                    name="course_comment"
                    value="{{ old('course_comment',$course['comment']) }}">
                </text-input-with-label>
                <checkbox-with-label
                    name="course_suspended"
                    {{ old('course_suspended',$course['suspended'])  == 1 ? 'is-checked' : '' }}>
                </checkbox-with-label>
                @foreach ($sessions as $session)
                <div class="mt-3">
                    @php
                    $thisSession = 'sessionNames['.($loop->index).']';
                    $thisSessionStartTime = 'sessionStartTimes['.($loop->index).']';
                    $thisSessionEndTime = 'sessionEndTimes['.($loop->index).']';
                    $thisSessionWeekOfTheMonth = 'sessionWeekOfTheMonth['.($loop->index).']';
                    $thisSessionId = 'sessionIds['.($loop->index+1).']';
                    $thisFacilitator = 'facilitators['.($loop->index).']';
                    $thisAlternateFacilitator = 'alternatefacilitators['.($loop->index).']';
                    $thisVenue = 'venues['.($loop->index).']';
                    $thisSessionMaxClassSize = 'sessionMaxClassSizes['.($loop->index).']';
                    $thisSessionMinClassSize = 'sessionMinClassSizes['.($loop->index).']';
                    $thisSessionValue = isset($session->id) ? "true" : "false";
                    $thisFacilitatorValue = $facilitators[$loop->index];
                    $thisAlternateFacilitatorValue = $alternate_facilitators[$loop->index];
                    $thisVenueValue = $venues[$loop->index];
                    $tday = $session->day_of_the_week;
                @endphp
                    <input
                        type="hidden"
                        name="{{ $thisSessionId }}"
                        value="{{ $session->id }}"
                    />
                    <subheading
                        v-if="{{ $thisSessionValue }}"
                        subheading="Session {{ $loop->index+1 }}:"  :width="5">
                    </subheading>
                    <text-input-with-label
                        v-if="{{ $thisSessionValue }}"
                        name="{{ $thisSession }}"
                        value="{{ old($thisSession, $session->name) }}">
                    </text-input-with-label>
                    <div v-if="{{ $thisSessionValue }}">
                        <single-entry-with-db-lookup
                            model="facilitator"
                            ajax-url="{{ url('onelineclosenamesearch') }}"
                            name="{{ $thisFacilitator }}"
                            :model-defaults="{{ $thisFacilitatorValue }}" 
                            @if ($loop->index == 7)
                                set-focus
                            @endif
                            >
                        </single-entry-with-db-lookup>
                    </div>
                    <div v-if="{{ $thisSessionValue }}">
                        <single-entry-with-db-lookup
                            model="facilitator"
                            ajax-url="{{ url('onelineclosenamesearch') }}"
                            name="{{ $thisAlternateFacilitator }}"
                            :model-defaults="{{ $thisAlternateFacilitatorValue }}">
                        </single-entry-with-db-lookup>
                    </div>
                    <div v-if="{{ $thisSessionValue }}">
                        <single-entry-with-db-lookup
                            model="venue"
                            ajax-url="{{ url('venuesearch') }}"
                            name="{{ $thisVenue }}"
                            :model-defaults="{{ $thisVenueValue }}">
                        </single-entry-with-db-lookup>
                    </div>
                    <div v-if="{{ $thisSessionValue }}">
                        <dropdown-with-label
                            name="heldon"
                            label="Held on"
                            selected-key={{$tday}}
                        >
                        </dropdown-with-label>
                    </div>
                    <horizontal-inputs-with-labels v-if="{{ $thisSessionValue }}"
                        input-type="time"
                        label1="From"
                        name1="{{ $thisSessionStartTime }}" 
                        value1="{{ old($thisSessionStartTime,$session->start_time) }}"
                        label2="To"
                        name2="{{ $thisSessionEndTime }}" 
                        value2="{{ old($thisSessionEndTime,$session->end_time) }}"
                    >
                    </horizontal-inputs-with-labels>
                    <horizontal-inputs-with-labels v-if="{{ $thisSessionValue }}"
                        label="Size:"
                        label1="Min:"
                        name1="{{ $thisSessionMaxClassSize }}" 
                        value1="{{ old($thisSessionMaxClassSize,$session->minimum_session_size) }}"
                        label2="Max:"
                        name2="{{ $thisSessionMinClassSize }}" 
                        value2="{{ old($thisSessionMinClassSize,$session->maximum_session_size) }}"
                    >
                   <div v-if="{{ $thisSessionValue }}">
                        <dropdown-with-label
                            name="{{ $thisSessionWeekOfTheMonth }}" 
                            label="Week"
                            selected-key="{{ old($thisSessionWeekOfTheMonth,$session->week_of_the_month) }}"
                            :options='[{key: 0, text: "Every week"},{key: 1, text: "Week 1"},{key: 2, text: "Week 2"},{key: 3, text: "Week 3"},{key: 4, text: "Week 4"}]'
                        >
                        </dropdown-with-label>
                    </div>
                </div>
                @endforeach
                @php
                    $isOtherSelected=old('effective_from', $effectiveFrom) == 'other' ? "true" : "false"
                @endphp
                <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
                <subheading
                    subheading="Changes effective from" :width="5">
                </subheading>
                <horizontal-radio-buttons-with-labels
                    name="effective_from"
                    :labels-and-values="{{$effectiveFromOptions}}"
                    checked-value="{{ old('effective_from', $effectiveFrom) }}">
                </horizontal-radio-buttons-with-labels>
                <radio-button-with-date-input
                    name="effective_from"
                    label="other"
                    value="other"
                    date-value="{{ old('effective_from_date',date('Y-m-d', strtotime($effectiveFromDate))) }}"
                    :is-checked="{{ $isOtherSelected }}">
                </radio-button-with-date-input>
                <div class="row mt-3">
                    <div>
                        <button type="submit" name="save" class="btn btn-primary btn-sm" value="true">Save</button>
                    </div>
                    <div class="ml-2">
                        <button type="submit" name="cancel" class="btn btn-primary btn-sm" value="true">Cancel</button>
                    </div>
                    <div class="ml-2">
                        <button type="button" name="new" class="btn btn-primary btn-sm" value="true">New session</button>
                    </div>
                </div>
                <input type="hidden" id="id" name="id" value="{{ old('id', $course['id']) }}"/>
                <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
                <input type="hidden" id="memberId" value="{{ $course->id }}" name="memberId" />
                <input type="hidden" id="state" value="{{ $state }}" name="state" />
            </div>
        </form>
        <script>
            var app2 = new Vue({
            el: '#details',
            data: {
                session: [],
            },
            created: function () {
                // if session[i] = true, show the session/row. Allow for a max of 6 sessions.
                for (i=0; i < 6; i++) { 
                    if (i < {{$numberOfSessions}}){
                        this.session[i] = true
                    } else {
                        this.session[i] = false
                    }
                }
            }
            })
        </script>
        @endif
    </div>
@endsection
