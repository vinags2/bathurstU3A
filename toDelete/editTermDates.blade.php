@extends('layouts.menu')

@section('content')
    <script src="{{ asset('dist/utilities.js') }}"> </script>
    <script src="{{ asset('dist/termdates.js') }}"> </script>
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit Term Dates for '.$currentYear])
        @include('partials.commonUI.showSuccessOrErrors')
        <form id="mainForm" method="post" action="{{ $action }}">
            @csrf
            <table class="table-sm" id="details">
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="numberOfTerms">number of terms:</label></td>
                    <td>
                        <input type="text" size="40" id="numberOfTerms" onchange="updateTotalNumberOfWeeks(this)" class="form-control w-25 @error('number_of_terms') is-invalid @enderror" name="number_of_terms" value="{{ old('number_of_terms',$number_of_terms) }}"
                        />
                        <div id="errorTooManyTerms" style="display: none;" class="invalid-feedback" role="alert">
                            <strong>There is a maximum limit of 9 terms</strong>
                        </div>
                        @error('number_of_terms')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr style="display: none;">
                    <td><label class="col-xs-3 col-form-label mr-2" for="totalNumberOfWeeks">total number of weeks:</label></td>
                    <td>
                        @php
                            $total_number_of_weeks = $number_of_terms * $weeks_in_term;
                        @endphp
                        <input type="text" size="40" id="totalNumberOfWeeks" class="form-control w-25 @error('total_number_of_weeks') is-invalid @enderror" name="total_number_of_weeks" value="{{ old('total_number_of_weeks',$total_number_of_weeks) }}"
                        />
                        @error('total_number_of_weeks')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="weeksInTerm">number of weeks per term:</label></td>
                    <td>
                        <input type="text" size="40" id="weeksInTerm" onchange="updateTotalNumberOfWeeks(this)" class="form-control w-25 @error('number_of_terms') is-invalid @enderror" name="weeks_in_term" value="{{ old('weeks_in_term',$weeks_in_term) }}"
                        />
                        <div id="errorTooManyWeeks" style="display: none;" class="invalid-feedback" role="alert">
                            <strong>There is a maximum limit of 45 weeks for classes</strong>
                        </div>
                        @error('weeks_in_term')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input  id="calculate_to-dates" type="checkbox" onChange="manualCalculation(this, {{ $weeks_in_term }})" name="calculate_to-dates" class="form-check-input ml-1">
                        <label class="form-check-label ml-4" for="calculate_to-dates">Manual calculation of 'to' dates</label>
                    </td>
                </tr>
                @php
                    $termDatesAsArray = json_decode($terms, TRUE)
                @endphp
                @foreach ($termDatesAsArray as $termDate)
                    <tr class="dateRow" style="display: block;">
                        @php
                            $thisTerm = 'term'.($loop->index+1);
                            $thisTermToDate = $thisTerm.'_enddate';
                        @endphp
                        <td><label class="col-xs-3 col-form-label mr-2" for="{{ $thisTerm }}">Term {{ $loop->index+1 }}:</label></td>
                        <td><input type="date" size="40" id="{{ $thisTerm }}" class="form-control fromDate" onchange="addWeeksToDates({{ $weeks_in_term }})" name="{{ $thisTerm }}" value="{{ old($thisTerm,date('Y-m-d', strtotime($termDate['start']))) }}"></td>
                            @php
                                $endDate = new DateTime($termDate['start']);
                                $interval = $weeks_in_term*7;
                                $interval = 'P'.$interval.'D';
                                $endDate->add(new DateInterval($interval));
                                $endDate = $endDate->format('Y-m-d');
                            @endphp
                        <td>to </td>
                        <td><input type="date" size="40" id="{{ $thisTermToDate }}" class="form-control toDate" name="{{ $thisTermToDate }}" readonly value="{{ old($thisTermToDate,date('Y-m-d', strtotime($termDate['end']))) }}"></td>
                    </tr>
                @endforeach
                @for ($i = 1; $i < (10-$number_of_terms); $i++)
                    <tr class="dateRow" style="display: none;">
                        @php
                            $j = $number_of_terms + $i;
                            $thisTerm = 'term'.($j);
                            $thisTermToDate = $thisTerm.'_enddate';
                        @endphp
                        <td><label class="col-xs-3 col-form-label mr-2" for="{{ $thisTerm }}">Term {{ $j }}:</label></td>
                        <td><input type="date" size="40" id="{{ $thisTerm }}" class="form-control fromDate" onchange="addWeeksToDates({{ $weeks_in_term }})" name="{{ $thisTerm }}" value=""></td>
                        <td>to </td>
                        <td><input type="date" size="40" id="{{ $thisTermToDate }}" class="form-control toDate" name="{{ $thisTermToDate }}" readonly value=""></td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="2" class="pt-4" >
                        <input type="hidden" id="editTermDates" name="editTermDates" value="true"/>
                        <input type="hidden" id="sessionId" name="sessionId" value="{{ $sessionId }}"/>
                        <button type="submit" name="save" class="btn btn-primary btn-sm" value="true">Save</button>
                        <button type="reset" class="btn btn-primary btn-sm">Reset</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
@endsection
