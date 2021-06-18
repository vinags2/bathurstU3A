@extends('layouts.menu')
@section('content')

    <script src="{{ asset('dist/pleasewait.js') }}"> </script>

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Weekly Timetable'])
    <div class="mt-3 mb-3">
        <label class="mt-1">Make a selection, and click Download</label>
    </div>
    <form action="{{ route('pdf', $reportId ?? 5) }}" autocomplete="off" method="GET">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="includeVenues" value="includeVenues" checked>
            <label class="form-check-label" for="includeVenues">
                Weekly timetable, including the venues
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="excludeVenues" value="excludeVenues">
            <label class="form-check-label" for="excludeVenues">
                Weekly timetable, in a larger font
            </label>
        </div>
        <div class="mt-3">
        <label>(Note that this can take about a minute to generate.)</label>
        </div>
        <button class="btn btn-outline-success mt-3" onclick="pleaseWait(this)" onfocusout="hidePleaseWait()" type="submit">Download</button>
    </form>
</div>
<br>
@endsection