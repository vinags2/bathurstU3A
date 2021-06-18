@extends('layouts.menu')
@section('content')

<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Course Information Sheet'])
    <div class="mt-3 mb-3">
        <label class="mt-1">Make a selection, and click Download</label>
    </div>
    <form action="{{ route('pdf', $reportId ?? 5) }}" autocomplete="off" method="GET">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="landscapeMode" value="landscapeMode" checked>
            <label class="form-check-label" for="landscapeMode">
                Course Information Sheet, in landscape mode
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="largerFont" value="largerFont">
            <label class="form-check-label" for="largerFont">
                Course Information Sheet, in a larger font
            </label>
        </div>
        <div class="mt-3">
        <label>(Note that this can take about a minute to generate.)</label>
        </div>
        <button class="btn btn-outline-success mt-3" type="submit">Download</button>
    </form>
</div>
<br>
@endsection