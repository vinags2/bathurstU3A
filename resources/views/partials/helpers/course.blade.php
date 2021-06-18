{{-- pass through $course and $id, both of which must exist --}}
@if (!empty($id))
    <a href="{{ route('report', ['id' => 25, 'courseId' => $id]) }}">
@endif
{{ $course }}
@if (!empty($id))
    </a>
@endif