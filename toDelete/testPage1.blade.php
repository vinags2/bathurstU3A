@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])

        {{ $person->name }} membership status for {{ config('myconfig.currentYear') }} is: {{ $first_membership_record ? 'member' : 'not a member' }}
        <p>{{ $person->name }} membership status for {{ config('myconfig.currentYear')-1 }} is: {{ $previous_membership_record ? 'member' : 'not a member' }}
        <p>{{ $person->name }} membership status is: {{ $is_contact_only ? 'contact only' : 'a member' }}
        <table>
            <tr><td>Year = {{ $year }}</td></tr>
            <tr><td><b>Person</b></td></tr>
            @foreach ($person->toArray() as $key => $item)
                <tr><td> {{ $key }} = {{ $item }}</td></tr>
            @endforeach
            <tr><td><b>Membership History</b></td></tr>
            @foreach ($membership_history as $item)
            @foreach ($item->toArray() as $key => $item2)
                <tr><td> {{ $key }} = {{ $item2 }} </td></tr>
            @endforeach
            <tr><td>--------------------------</td></tr>
            @endforeach
            <tr><td><b>Current Member</b></td></tr>
            <tr><td> {{ $current_member }} </td></tr>
        </table>
    </div>
@endsection
