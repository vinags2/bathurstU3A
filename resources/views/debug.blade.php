@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Debug Page'])
        <div class="row">
            <div>
            <!-- <div class="col-md-4">
                $html = new Html()
                $a = $html->createErrorMessage()
                $html->show($a) -->
            </div>
        </div>
        <div class="row">
            <b>Variables passed:</b><br><br>
        </div>
        <div class="row">
            @if (empty($variables))
                No variables passed to this view.
            @else
                @foreach ($variables as $key => $variable) 
                    {{ $key }} => {{ $variable }}<br>
                @endforeach
            @endif
           <br>
        </div>
        <div class="row">
            <b>Request variables:</b><br><br>
        </div>
        <div class="row">
            <u>POST variables:</u><br><br>
        </div>
        <div class="row">
            @if (empty(request()->all()))
                No variables passed to this view.
            @else
                @foreach (request()->all() as $key => $val)
                    {{ $key }} => {{ $val }}<br>
                @endforeach
            @endif
        </div>
        <div class="row">
            <b>Session variables:</b><br><br>
        </div>
        <div class="row">
            @if (empty(session()->all()))
                No variables passed to this view.
            @else
                @foreach (session()->all() as $key => $val)
                    {{ $key }} => {{ $val }}<br>
                @endforeach
            @endif
        </div>
    </div>
@endsection
