@extends('layouts.menu')

@section('content')
<script src="{{ asset('dist/utilities.js') }}"> </script>
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script> -->
<script src = "{{ asset('js/vue.js') }}"></script>
<script src = "{{ asset('js/lodash.min.js') }}"></script>
<script src = "{{ asset('js/axios.min.js') }}"></script>
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
<script src = "{{ asset('js/HorizontalInputWithLabel.js') }}"></script>

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit, or new Course'])
    @include('partials.commonUI.showSuccessOrErrors')
    @if (!$showDetails)
        <div id = "course">
            <singleentry model="course" ajaxurl='{{ $searchUrl }}' allownewmodels="true"></singleentry>
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
                    :error={{ $errors->has('course_name') ? "true" : "false" }}
                    set-focus >
                </text-input-with-label>
                <text-area-with-label
                    title="A description of the course, for prospective class members. Keep the description to about 200 characters."
                    name="course_description"
                    :error={{ $errors->has('course_description') ? "true" : "false" }}
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
                    // The facilitators array is an array of objects containing id and name
                    $facilitatorNameName = 'facilitatorname'.$loopIndex;
                    $facilitatorIdName = 'facilitatorid'.$loopIndex;
                    $oldFacilitatorName = 'facilitatorname'.$loopIndexInDotNotation;
                    $oldFacilitatorId = 'facilitatorid'.$loopIndexInDotNotation;
                    $facilitator = $facilitators[$loop->index];

                    // Alternate Facilitator
                    $alternateFacilitatorNameName = 'alternatefacilitatorname'.$loopIndex;
                    $alternateFacilitatorIdName = 'alternatefacilitatorid'.$loopIndex;
                    $oldAlternateFacilitatorName = 'alternatefacilitatorname'.$loopIndexInDotNotation;
                    $oldAlternateFacilitatorId = 'alternatefacilitatorid'.$loopIndexInDotNotation;
                    $alternateFacilitator = $alternate_facilitators[$loop->index];

                    // Venue
                    $venueNameName = 'venuename'.$loopIndex;
                    $venueIdName = 'venueid'.$loopIndex;
                    $oldVenueName = 'venuename'.$loopIndexInDotNotation;
                    $oldVenueId = 'venueid'.$loopIndexInDotNotation;
                    $venue = $venues[$loop->index];

                    // Session description
                    $descriptionName = 'sessionDescriptions'.$loopIndex;
                    $oldDescription = 'sessionDescriptions'.$loopIndexInDotNotation;

                    // Class sizes
                    $maxClassSizeName = 'sessionMaxClassSizes'.$loopIndex;
                    $minClassSizeName = 'sessionMinClassSizes'.$loopIndex;
                    $oldMaxClassSize = 'sessionMaxClassSizes'.$loopIndexInDotNotation;
                    $oldMinClassSize = 'sessionMinClassSizes'.$loopIndexInDotNotation;

                    // Day of the week
                    $dayOfTheWeekName = 'sessionDayOfTheWeeks'.$loopIndex;
                    $oldDayOfTheWeek = 'sessionDayOfTheWeeks'.$loopIndexInDotNotation;

                    // Week of the month
                    $weekOfTheMonthName = 'sessionWeekOfTheMonths['.($loop->index).']';
                    $oldWeekOfTheMonth = 'sessionWeekOfTheMonths.'.($loop->index);

                    // Start and End times
                    $startTimeName = 'sessionStartTimes'.$loopIndex;
                    $endTimeName = 'sessionEndTimes'.$loopIndex;
                    $oldStartTime = 'sessionStartTimes'.$loopIndexInDotNotation;
                    $oldEndTime = 'sessionEndTimes'.$loopIndexInDotNotation;

                    //Rolls
                    $rollTypeName = 'sessionRollTypes'.$loopIndex;
                    $printContactsName = 'sessionPrintContacts'.$loopIndex;
                    $oldRollType = 'sessionRollTypes'.$loopIndexInDotNotation;
                    $oldPrintContacts = 'sessionPrintContacts'.$loopIndexInDotNotation;
                    $rollType = $session->roll_type % 64;
                    $printContact = $session->roll_type / 64 >= 1 ? "1" : "0";

                    // Suspended and deleted
                    $suspendedName = 'sessionSuspendeds'.$loopIndex;
                    $deletedName = 'sessionDeleteds'.$loopIndex;
                    $oldSuspended = 'sessionSuspendeds'.$loopIndexInDotNotation;
                    $oldDeleted = 'sessionDeleteds'.$loopIndexInDotNotation;

                    // Notes/comments
                    $commentName = 'sessionComments'.$loopIndex;
                    $oldComment = 'sessionComments'.$loopIndexInDotNotation;

                    // Active terms
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
                        :error={{ $errors->has('sessionNames.'.$loop->index) ? "true" : "false" }}
                        value="{{ old($oldName, $session->name) }}">
                    </text-input-with-label>
                    <div >
                        <single-entry-with-db-lookup
                            model="facilitator"
                            ajax-url="{{ url('onelineclosenamesearch') }}"
                            nameid="{{ $facilitatorIdName }}"
                            namename="{{ $facilitatorNameName }}"
                            :model-id-default="{{ old($oldFacilitatorId, $facilitator->id) ?? -1 }}" 
                            model-name-default="{{ old($oldFacilitatorName, $facilitator->name) }}" 
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
                            nameid="{{ $alternateFacilitatorIdName }}"
                            namename="{{ $alternateFacilitatorNameName }}"
                            :model-id-default="{{ old($oldAlternateFacilitatorId, $alternateFacilitator->id) ?? -1 }}" 
                            model-name-default="{{ old($oldAlternateFacilitatorName, $alternateFacilitator->name) ?? '' }}" 
                            >
                        </single-entry-with-db-lookup>
                    </div>
                    <div >
                        <single-entry-with-db-lookup
                            model="venue"
                            ajax-url="{{ url('venuesearch') }}"
                            nameid="{{ $venueIdName }}"
                            namename="{{ $venueNameName }}"
                            :model-id-default="{{ old($oldVenueId, $venue->id) ?? -1 }}" 
                            model-name-default="{{ old($oldVenueName, $venue->name) ?? '' }}" 
                            >
                        </single-entry-with-db-lookup>
                    </div>
                    <text-input-with-label
                        name="{{ $descriptionName }}"
                        title="This description of the session is appended to the course description. Keep it brief."
                        value="{{ old($oldDescription, $session->description) }}"
                        :error={{ $errors->has('sessionDescriptions.'.$loop->index) ? "true" : "false" }}
                        :max-length=100
                        label="Description">
                    </text-input-with-label>
                    <horizontal-inputs-with-labels 
                        label="Size"
                        label1="Min"
                        name1="{{ $minClassSizeName }}" 
                        value1="{{ old($oldMinClassSize,$session->minimum_session_size) }}"
                        title1="Minimum class size (0 or empty for no minimum)"
                        :error={{ $errors->has('sessionMinClassSizes.'.$loop->index) ? "true" : "false" }}
                        label2="Max"
                        name2="{{ $maxClassSizeName }}" 
                        value2="{{ old($oldMaxClassSize,$session->maximum_session_size) }}"
                        title2="Maximum class size (0 or empty for no maximum)"
                    >
                    </horizontal-inputs-with-labels>
                    <div >
                        <dropdown-with-label
                            name="{{ $dayOfTheWeekName }}"
                            label="Held on"
                            title="On which day of the week does the session run?"
                           selected-key="{{ old($oldDayOfTheWeek, $session->day_of_the_week)}}"
                        >
                        </dropdown-with-label>
                    </div>
                    <div >
                        <dropdown-with-label
                            name="{{ $weekOfTheMonthName }}" 
                            label="Week"
                            title="If the session does not run every week of the month, select which week of the month."
                            selected-key="{{ old($oldWeekOfTheMonth,$session->week_of_the_month)}}"
                            :options='[{key: 0, text: "Every week"},{key: 1, text: "Week 1"},{key: 2, text: "Week 2"},{key: 3, text: "Week 3"},{key: 4, text: "Week 4"}]'
                        >
                        </dropdown-with-label>
                    </div>
                    <horizontal-inputs-with-labels 
                        input-type="time"
                        label1="From"
                        name1="{{ $startTimeName }}" 
                        value1="{{ old($oldStartTime,$session->start_time) }}"
                        title1="The time of the day that the class starts. Must be earlier than the end time."
                        :error={{ $errors->has('sessionEndTimes.'.$loop->index) ? "true" : "false" }}
                        label2="To"
                        name2="{{ $endTimeName }}" 
                        value2="{{ old($oldEndTime,$session->end_time) }}"
                        title2="The time of the day that the class ends. Must be later than the start time."
                    >
                    </horizontal-inputs-with-labels>
                    @php
                        $title = "In which terms is the session running? If all year, with no term breaks, select 'all year'"
                    @endphp
                    <div class="row">
                        <div class="col-1 mr-3" title="{{$title}}">
                            Terms
                        </div>
                        @foreach ($defaultActiveTermsForTheSession as $key => $defaultActiveTerm)
                                @php
                                    $checkboxName = 'sessionActiveTerms['.($loop->parent->index).']'.'['.($loop->index).']';
                                    $checkboxArray = 'sessionActiveTerms['.($loop->parent->index).']';
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
                                <input type="checkbox" class="mr-1" title="{{$title}}"
                                name="{{$checkboxName}}"
                                v-model="defaultterms[{{$loop->parent->index}}][{{$loop->index}}]"
                                @change="setallyear({{$loop->parent->index}})"
                                value=1
                                />
                                <label class="mr-3" title="{{$title}}">
                                    Term {{ $loop->index + 1 }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @php
                        $checkboxAllYearName = 'sessionAllYearInstead['.($loop->index).']';
                        $userEnteredAllYearValue = 'sessionAllYearInstead.'.($loop->index);
                    @endphp
                    <div class="row">
                        <div class="col-1 mr-3" title="{{$title}}"></div>
                        <div>
                            <input type="checkbox" class="mr-1" title="{{$title}}"
                            @if ((is_array(old($userEnteredActiveTermsForTheSession)) ? old($userEnteredValue, 0) : $defaultActiveTerm) == 1)
                                    checked
                            @endif
                            name="{{$checkboxAllYearName}}"
                            v-model="defaultallyearinstead[{{$loop->index}}]"
                            @change="setallyear({{$loop->index}}, true)"
                            value=1
                            />
                            <label class="mr-3" title="{{$title}}">
                                all year 
                            </label>
                        </div>
                    </div>
                    <dropdown-and-checkbox-with-label
                        name1="{{ $rollTypeName }}" 
                        label1="Rolls"
                        title1="Roll types are: normal, generic (no names on roll), 2-page generic, monthly (one per month), no roll"
                        selected-key="{{ old($oldRollType,$rollType) }}"
                        :options="{{ $rollTypeOptions }}"
                        label2="Contacts"
                        name2="{{ $printContactsName }}"
                        title2="Include contact details when printing rolls"
                        @if (old($oldRollType,"undefined") != "undefined")
                            :is-checked="{{ old($oldPrintContacts,0)}}"
                        @else
                            :is-checked="{{ old($printContact, 1)}}"
                        @endif
                    >
                    </dropdown-and-checkbox-with-label>
                    <horizontal-inputs-with-labels 
                        input-type="checkbox"
                        label1="Suspended"
                        name1="{{ $suspendedName }}" 
                        :checked1={{ old($oldSuspended,$session->suspended)  == 1 ? 1 : 0 }}
                        title1="Is this session not running this term, or this year?"
                        label2="Delete"
                        name2="{{ $deletedName }}" 
                        :checked2={{ old($oldDeleted,$session->deleted)  == 1 ? 1 : 0 }}
                        title2="Is this session permanently no longer offerred? Deleting the session cannot be undone."
                        >
                    </horizontal-inputs-with-labels>
                    <div >
                        <text-input-with-label
                            label="Notes"
                            title="Notes. Not visible except by authorised users. Maximum of 100 characters."
                            name="{{ $commentName }}" 
                            :error={{ $errors->has('sessionComments.'.$loop->index) ? "true" : "false" }}
                            :max-length=100
                            value="{{ old($oldComment,$session->comment) }}">
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
                    <input type="hidden" id="numberOfTerms" name="numberOfTerms" :value="numberOfTerms"/>
                    <input type="hidden" id="state" value="{{ $state }}" name="state" />
                    <input type="hidden" :value="newSessions" name="numberOfNewSessions" />
                </div>
            </div>
        </form>
         @php
          dd(
              'userterms = ', json_encode(old('sessionActiveTerms')),
              'defaultterms = ', json_encode($activeTerms),
              'userallyearinstead = ', json_encode(old('sessionAllYearInstead')),
              'defaultallyearinstead = ', json_encode($allYearInsteadOfTerms)
            )
           @endphp
        <script>
            var app2 = new Vue({
            el: '#details',
            data: {
                newSessions: 0,
                totalNumberOfSessions: {{ count($sessions) }},
                numberOfTerms: 0,
                defaultterms: {{ json_encode($activeTerms) }},
                userterms: {{ json_encode(old('sessionActiveTerms')) }}, 
                defaultallyearinstead: {{ json_encode($allYearInsteadOfTerms) }},
                userallyearinstead: {{ json_encode(old('sessionAllYearInstead')) }}
            },
            created: function () {
                this.newSessions = {{ $numberOfNewSessions }},
                this.numberOfTerms = {{ old('numberOfTerms', $numberOfTerms) }},
                this.initTerms()
            },
            methods: {
                incrementNewSessions: function() {
                    this.newSessions += 1
                },
                setallyear: function(session, isallyear = false) {
                  if (isallyear) {
                    if (this.defaultallyearinstead[session]) {
                      this.setcheckboxes(session);
                    } else {
                      this.setcheckboxes(session,true);
                    }
                  } else {
                    this.defaultallyearinstead[session] = this.notermsselected(session);
                  }
                },
                setcheckboxes(session, torf = false) {
                  this.defaultterms[session].forEach((i,key) => {
                    this.defaultterms[session][key] = torf;
                  });
                },
                notermsselected(session) {
                    const found = this.defaultterms[session].find(element => element == 1);
                    return !found;
                },
                initTerms() {
                    for (i=0; i < this.totalNumberOfSessions; i++) {
                        this.defaultallyearinstead[i] = this.userallyearinstead ? this.userallyearinstead[i] : this.defaultallyearinstead[i];
                        for (j=0; j < this.numberOfTerms; j++) {
                            this.defaultterms[i][j] = this.userterms[i] ? this.userterms[i][j] : this.defaultterms[i][j];
                        }
                    }
                }
            }
            })
        </script>
        @endif
    </div>
@endsection
