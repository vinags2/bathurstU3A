@extends('layouts.menu')
@section('content')

<div class="container">
    <div class="mt-3 mb-3">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Class Rolls'])
        <label class="mt-1">Make a selection, and click Download</label>
    </div>
        <form action="{{ route('pdf', $reportId ?? 5) }}" autocomplete="off" method="GET">
        <div class="form-row mt-3 mb-3">
            <select id="item" name="sessionId" class="form-control custom-select w-auto">
            <option value="-1">All Class Rolls</option>
                @foreach ($sessions ?? [] as $item)
                    <option value="{{ $item['id'] }}"
                        @if ($selectedSession === $item['name'])
                            selected
                        @endif
                        >{{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="rollsOnly" value="rollsOnly" checked>
            <label class="form-check-label" for="rollsOnly">
                Rolls
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="contactDetailsOnly" value="contactDetailsOnly">
            <label class="form-check-label" for="contactDetailsOnly">
                Contact Details
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="options" id="both" value="both">
            <label class="form-check-label" for="both">
                both
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