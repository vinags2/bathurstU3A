{{-- pass through $member and $id, both of which must exist --}}
@if(auth()->user()->can('view-member-details', $id) or (!empty($memberDetailsReport)))
@if (empty($memberDetailsReport))
    @php
        $memberDetailsReport = 19;
    @endphp
@endif
@if (!empty($id))
    <a href="{{ route('report', ['id' => $memberDetailsReport, 'memberId' => $id]) }}">
@endif
{{ $member }}
@if (!empty($id))
    </a>
@endif
@else
{{ $member }}
@endif