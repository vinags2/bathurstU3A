{{-- pass through $venue and $id, both of which must exist --}}
@if (!empty($id))
    <a href="{{ route('report', ['id' => 26, 'venueId' => $id]) }}">
@endif
{{ $venue }}
@if (!empty($id))
    </a>
@endif