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
<script src = "{{ asset('js/DropdownAndCheckboxWithLabel.js') }}"></script>
<script src = "{{ asset('js/HorizontalRadioButtonsWithLabels.js') }}"></script>
<script src = "{{ asset('js/HorizontalInputsWithLabels.js') }}"></script>

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit, or new Course'])
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
                    title="The name of the course. Long names will not print well."
                    value="{{ old('course_name',$course['name']) }}"
                    set-focus >
                </text-input-with-label>
                <text-area-with-label
                    title="A description of the course, for prospective class members. Keep the description to about 200 characters."
                    name="course_description"
                    value="{{ old('course_description',$course['description']) }}" >
                </text-area-with-label>
                <horizontal-inputs-with-labels
                    input-type="checkbox"
                    label1="Suspended"
                    name1="course_suspended" 
                    title1="Is the course not running this term, or this year?"
                    :checked1={{ old('course_suspended',$course['deleted'])  == 1 ? 1 : 0 }}
                    label2="Delete"
                    name2="course_deleted" 
                    title2="Is the course permanently no longer offerred? Deleting the course cannot be undone."
                    :checked2={{ old('course_deleted',$course['deleted'])  == 1 ? 1 : 0 }}
                    >
                </horizontal-inputs-with-labels>
                <text-input-with-label
                    label="Notes"
                    title="Notes. Not visible except by authorised users. Maximum of 100 characters."
                    name="course_comment"
                    :max-length=100
                    value="{{ old('course_comment',$course['comment']) }}">
                </text-input-with-label>
                @foreach ($sessions as $session)
                <div class="mt-3">
                    @php
                    $thisSessionStartTime = 'sessionStartTimes['.($loop->index).']';
                    $thisSessionEndTime = 'sessionEndTimes['.($loop->index).']';
                    $thisSessionWeekOfTheMonth = 'sessionWeekOfTheMonths['.($loop->index).']';
                    $thisSessionDayOfTheWeek = 'sessionDayOfTheWeeks['.($loop->index).']';
                    $thisSessionDescription = 'sessionDescriptions['.($loop->index).']';
                    $thisSessionComment = 'sessionComments['.($loop->index).']';
                    $thisSessionSuspended = 'sessionSuspendeds['.($loop->index).']';
                    $thisSessionDeleted = 'sessionDeleteds['.($loop->index).']';
                    $thisSessionRollType = 'sessionRollTypes['.($loop->index).']';
                    $thisSessionPrintContacts = 'sessionPrintContacts['.($loop->index).']';
                    $thisAlternateFacilitator = 'alternatefacilitators['.($loop->index).']';
                    $thisVenue = 'venues['.($loop->index).']';
                    $thisSessionMaxClassSize = 'sessionMaxClassSizes['.($loop->index).']';
                    $thisSessionMinClassSize = 'sessionMinClassSizes['.($loop->index).']';
                    $thisFacilitatorValue = $facilitators[$loop->index];
                    $thisAlternateFacilitatorValue = $alternate_facilitators[$loop->index];
                    $thisVenueValue = $venues[$loop->index];
                    $thisSessionPrintContactsValue = $session->print_contact_details ? "true" : "false";
                    $thisSessionStartTimeOld = 'sessionStartTimes.'.($loop->index);
                    $thisSessionEndTimeOld = 'sessionEndTimes.'.($loop->index);
                    $thisSessionWeekOfTheMonthOld = 'sessionWeekOfTheMonths.'.($loop->index);
                    $thisSessionDayOfTheWeekOld = 'sessionDayOfTheWeeks.'.($loop->index);
                    $thisSessionIdOld = 'sessionIds.'.($loop->index);
                    $thisSessionDescriptionOld = 'sessionDescriptions.'.($loop->index);
                    $thisSessionCommentOld = 'sessionComments.'.($loop->index);
                    $thisSessionSuspendedOld = 'sessionSuspendeds.'.($loop->index);
                    $thisSessionDeletedOld = 'sessionDeleteds.'.($loop->index);
                    $thisSessionRollTypeOld = 'sessionRollTypes.'.($loop->index);
                    $thisSessionPrintContactsOld = 'sessionPrintContactss.'.($loop->index);
                    $thisFacilitatorOld = 'Facilitators.'.($loop->index);
                    $thisAlternateFacilitatorOld = 'AlternateFacilitators.'.($loop->index);
                    $thisVenueOld = 'Venues.'.($loop->index);
                    $thisSessionMaxClassSizeOld = 'sessionMaxClassSizes.'.($loop->index);
                    $thisSessionMinClassSizeOld = 'sessionMinClassSizes.'.($loop->index);

                    // variables to make it more readable
                    // 'old...' is the user entered data retrieved using the 'old' helper
                    // '....Name' is the name of the control, returned in the request
                    $loopIndex = '['.$loop->index.']';
                    $loopIndexInDotNotation = '.'.$loop->index;
                    // Session Name
                    $nameName = 'sessionNames'.$loopIndex;
                    $oldName = 'sessionNames'.$loopIndexInDotNotation;
                    // Session id
                    // There is not 'old' session id...the id does not change.
                    $sessionId = 'sessionIds'.$loopIndex;
                    // Facilitator
                    // The facilitators array is an array of json encoded objects containing id and name
                    // Only the facilitator id is saved
                    $facilitatorNameName = 'facilitatorname'.$loopIndex;
                    $facilitatorIdName = 'facilitatorid'.$loopIndex;
                    $defaultActiveTermsForTheSession = $activeTerms[$loop->index];
                @endphp
                    <input
                        type="hidden"
                        name="{{ $sessionId }}"
                        value="{{ $session->id }}"
                    />
                    <subheading
                        subheading="Session {{ $loop->index+1 }}:"  :width="5">
                    </subheading>
                    <text-input-with-label
                        name="{{ $nameName }}"
                        title="The name of this session. A course can be run several times a week. Each session must have a name. It can be the same as the course name."
                        value="{{ old($oldName, $session->name) }}">
                    </text-input-with-label>
                    <div >
                        <single-entry-with-db-lookup
                            model="facilitator"
                            ajax-url="{{ url('onelineclosenamesearch') }}"
                            nameid="{{ $facilitatorIdName }}"
                            namename="{{ $facilitatorNameName }}"
                            :model-id-default="{{ $thisFacilitatorValue->id }}" 
                            :model-name-default="{{ $thisFacilitatorValue->name }}" 
                            @if ($loop->index == 7)
                                set-focus
                            @endif
                            >
                        </single-entry-with-db-lookup>
                    </div>
                    <div >
                        <single-entry-with-db-lookup
                            model="facilitator"
                            ajax-url="{{ url('onelineclosenamesearch') }}"
                            name="{{ $thisAlternateFacilitator }}"
                            :model-defaults="{{ $thisAlternateFacilitatorValue }}">
                        </single-entry-with-db-lookup>
                    </div>
                    <div >
                        <single-entry-with-db-lookup
                            model="venue"
                            ajax-url="{{ url('venuesearch') }}"
                            name="{{ $thisVenue }}"
                            :model-defaults="{{ $thisVenueValue }}">
                        </single-entry-with-db-lookup>
                    </div>
                    <text-input-with-label
                        name="{{ $thisSessionDescription }}"
                        title="This description of the session is appended to the course description. Keep it brief."
                        value="{{ old($thisSessionDescriptionOld, $session->description) }}"
                        :max-length=100
                        label="Description:">
                    </text-input-with-label>
                    <horizontal-inputs-with-labels 
                        label="Size:"
                        label1="Min:"
                        name1="{{ $thisSessionMinClassSize }}" 
                        value1="{{ old($thisSessionMinClassSizeOld,$session->minimum_session_size) }}"
                        title1="Minimum class size (0 or empty for no minimum)"
                        label2="Max:"
                        name2="{{ $thisSessionMaxClassSize }}" 
                        value2="{{ old($thisSessionMaxClassSizeOld,$session->maximum_session_size) }}"
                        title2="Maximum class size (0 or empty for no maximum)"
                    >
                    </horizontal-inputs-with-labels>
                    <div >
                        <dropdown-with-label
                            name="{{ $thisSessionDayOfTheWeek }}"
                            label="Held on"
                            title="On which day of the week does the session run?"
                           selected-key="{{ old($thisSessionDayOfTheWeekOld, $session->day_of_the_week)}}"
                        >
                        </dropdown-with-label>
                    </div>
                    <div >
                        <dropdown-with-label
                            name="{{ $thisSessionWeekOfTheMonth }}" 
                            label="Week"
                            title="If the session does not run every week of the month, select which week of the month."
                            selected-key="{{ old($thisSessionWeekOfTheMonthOld,$session->week_of_the_month)}}"
                            :options='[{key: 0, text: "Every week"},{key: 1, text: "Week 1"},{key: 2, text: "Week 2"},{key: 3, text: "Week 3"},{key: 4, text: "Week 4"}]'
                        >
                        </dropdown-with-label>
                    </div>
                    <horizontal-inputs-with-labels 
                        input-type="time"
                        label1="From"
                        name1="{{ $thisSessionStartTime }}" 
                        value1="{{ old($thisSessionStartTimeOld,$session->start_time) }}"
                        title1="The time of the day that the class starts. Must be earlier than the end time."
                        label2="To"
                        name2="{{ $thisSessionEndTime }}" 
                        value2="{{ old($thisSessionEndTimeOld,$session->end_time) }}"
                        title2="The time of the day that the class ends. Must be later than the start time."
                    >
                    </horizontal-inputs-with-labels>
                    <div class="row">
                        <div class="col-1 mr-3">
                            Terms:
                        </div>
                        @foreach ($defaultActiveTermsForTheSession as $key => $defaultActiveTerm)
                                @php
                                    $checkboxName = 'sessionActiveTerms['.($loop->parent->index).']'.'['.($loop->index).']';
                                    $userEnteredValue = 'sessionActiveTerms.'.($loop->parent->index).'.'.($loop->index);
                                    $userEnteredActiveTermsForTheSession = 'sessionActiveTerms.'.$loop->parent->index;
                                @endphp
                                @if (($loop->index != 0) and ($loop->index % 4 == 0))
                                    </div>
                                    <div class="row">
                                        <div class="col-1 mr-3">
                                        </div>
                                @endif
                            <div>
                                <input type="checkbox" class="mr-1"
                                @if ((is_array(old($userEnteredActiveTermsForTheSession)) ? old($userEnteredValue, 0) : $defaultActiveTerm) == 1)
                                        checked
                                @endif
                                name="{{$checkboxName}}"
                                value="1"
                                />
                                <label class="mr-3"> Term {{$loop->index + 1}} </label>
                            </div>
                        @endforeach
                    </div>
                    <dropdown-and-checkbox-with-label
                        name1="{{ $thisSessionRollType }}" 
                        label1="Rolls"
                        title1="Roll types are: normal, generic (no names on roll), 2-page generic, monthly (one per month), no roll"
                        selected-key="{{ old($thisSessionRollTypeOld,$session->roll_only) }}"
                        :options="{{ $rollTypeOptions }}"
                        label2="Contacts"
                        name2="{{ $thisSessionPrintContacts }}"
                        title2="Include contact details when printing rolls"
                        :is-checked="{{ old($thisSessionPrintContactsOld, $thisSessionPrintContactsValue)}}"
                    >
                    </dropdown-and-checkbox-with-label>
                    <horizontal-inputs-with-labels 

                        input-type="checkbox"
                        label1="Suspended"
                        name1="{{ $thisSessionSuspended }}" 
                        :checked1={{ old($thisSessionSuspendedOld,$session->suspended)  == 1 ? 1 : 0 }}
                        title1="Is this session not running this term, or this year?"
                        label2="Delete"
                        name2="{{ $thisSessionDeleted }}" 
                        :checked2={{ old($thisSessionDeletedOld,$session->deleted)  == 1 ? 1 : 0 }}
                        title2="Is this session permanently no longer offerred? Deleting the session cannot be undone."
                        >
                    </horizontal-inputs-with-labels>
                    <div >
                        <text-input-with-label
                            label="Notes"
                            title="Notes. Not visible except by authorised users. Maximum of 100 characters."
                            name="{{ $thisSessionComment }}" 
                            :max-length=100
                            value="{{ old($thisSessionCommentOld,$session->comment) == 1 ? 1 : 0}}">
                        </text-input-with-label>
                    </div>
                </div>
                @endforeach
                @php
                    $isOtherSelected=old('effective_from', $effectiveFrom) == 'other' ? "true" : "false"
                @endphp
                <div class="mt-3">
                    <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
                    <subheading
                        subheading="Changes effective from" :width="5">
                    </subheading>
                    <horizontal-radio-buttons-with-labels
                        name="effective_from"
                        title="When do your changes take effect? Next term or next year maybe? If other, select the date for when they take effect."
                        :labels-and-values="{{$effectiveFromOptions}}"
                        checked-value="{{ old('effective_from', $effectiveFrom) }}">
                    </horizontal-radio-buttons-with-labels>
                    <radio-button-with-date-input
                        name="effective_from"
                        label="other"
                        value="other"
                        title="When do your changes take effect? Next term or next year maybe? If other, select the date for when they take effect."
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
                            <button type="submit" name="new" class="btn btn-primary btn-sm" @click="incrementNewSessions()" value="true">New session</button>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id" value="{{ old('id', $course['id']) }}"/>
                    <input type="hidden" id="numberOfSessions" name="numberOfSessions" value="{{ old('numberOfSessions', $numberOfSessions) }}"/>
                    <input type="hidden" id="memberId" value="{{ $course->id }}" name="memberId" />
                    <input type="hidden" id="state" value="{{ $state }}" name="state" />
                    <input type="hidden" :value="newSessions" name="numberOfNewSessions" />
                </div>
            </div>
        </form>
        <script>
            var app2 = new Vue({
            el: '#details',
            data: {
                session: [],
                newSessions: 0
            },
            created: function () {
                this.newSessions = {{ $numberOfNewSessions }}
                // if session[i] = true, show the session/row. Allow for a max of 6 sessions.
                for (i=0; i < 6; i++) { 
                    if (i < {{$numberOfSessions}}){
                        this.session[i] = true
                    } else {
                        this.session[i] = false
                    }
                }
            },
            methods: {
                incrementNewSessions: function() {
                    this.newSessions += 1
                }
            }
            })
        </script>
        @endif
    </div>
@endsection
