{{--
    variables required:
    $name (eg courseId), $items (with a 'name' attribute) to iterate through, and $selectedOption (which is option selected in the select box)
--}}
@can('basic member')
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Details about '.$selectedOption])
@else
    @include('partials.commonUI.pageHeading', ['pageHeading' => 'Details about'])
    <form action="" autocomplete="off" method="GET">
        <div class="form-row mt-3 mb-3 justify-content-center">
            <!-- <h5><label for="item" class="mt-1">Report for </label></h5> -->
            <select id="item" name="{{ $name }}" class="ml-2 form-control custom-select w-auto " onchange="this.form.submit()">
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
    </form>
@endcan