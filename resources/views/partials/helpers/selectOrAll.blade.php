{{--
    variables required:
    $name (eg courseId), $items (with a 'name' attribute) to iterate through, $heading (eg 'class rolls'). and $selectedOption (which is option selected in the select box)
--}}
<div class="mt-3 mb-3">
    <h5><label class="mt-1">{{ $heading }}</label></h5>
    <label class="mt-1">Make a selection, and click Download</label>
</div>
<form action="{{ route('pdf', $reportId) }}" autocomplete="off" method="GET">
    <div class="form-row mt-3 mb-3">
        <select id="item" name="{{ $name }}" class="form-control custom-select w-auto">
        <option value="-1">All {{ $heading }}</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}"
                    @if ($selectedOption === $item->name)
                        selected
                    @endif
                    >{{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-outline-success mt-3" type="submit">Download</button>
</form>