@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Security Message'])

        <div class="mt-2 mb-3 ml-5 alert-info text-center">
            <h5>You are not authorised to access this page</h5>
        </div>
    </div>
@endsection
