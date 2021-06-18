@extends('layouts.menu')

@section('content')

<div class="container">
    <div class="row mt-1">
        <div>
        <!-- <div class="col-md-4">
            $html = new Html()
            $a = $html->createErrorMessage()
            $html->show($a) -->
        </div>
    </div>
    <div class="row mt-1">
        <div>
            <!-- <div class="col-md-4"> -->
            <h4>Email has been verified</h4>

            <p>Congratulations. Your email has been verified.</p>

            <p>Please <a href="{{ route('login') }}">login</a> to continue...</p>
        </div>
    </div>
</div>

@endsection
