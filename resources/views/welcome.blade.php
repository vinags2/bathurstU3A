@extends('layouts.menu')

@section('content')
    <div class="container">
        <div class="row">
            <div>
            <!-- <div class="col-md-4">
                $html = new Html()
                $a = $html->createErrorMessage()
                $html->show($a) -->
            </div>
        </div>
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'About the Bathurst U3A Database'])
        @include('partials.commonUI.showSuccessOrErrors')
        <div class="row">
            <div>
                <p>The Bathurst U3A Database was created in 2017. The objective was to make the club's data
                (the membership contact details, the class lists, venue information, etc) accessible to all committee members
                in a timely manner, and to reduce the workload on the hardworking committee.</p>

                <p>You are now using Version 2, which began development in 2019. Version 2 came about because:</p>
                <ol>
                    <li>the application was growing, and needed to be re-written more rigorously and professionally</li>
                    <li>the interface needed to be 'modernised', and be usable by desktops, tablets and phones</li>
                </ol>
                
                <p>To use the database, use the menu bar on the top of the screen. If you are using a device with a small screen,
                the menu can be found by tapping the 3 horizontal bars at the top-left.</p>

                <p>You must initially <a href="{{ route('login') }}">login</a> to the database, 
                <a href="{{ route('register') }}">register</a> to use this database if you are an existing Bathurst U3A member, 
                or <a href="{{ route('join') }}">join</a> the Bathurst U3A.</p>

                <p>For suggestions, feedback, etc, send an email to the <a href="mailto:webadministator@bathurstu3a.com">
                database administrator</a>.</p>
                
                <p>Things that you can do with the database (depending upon your access) include:</p>
                <ul>
                    <li>get information on the members, courses, venues, etc in all sorts of ways</li>
                    <li>create a list for creating a mailing list</li>
                    <li>download the information for use in a spreadsheet such as Excel and Numbers</li>
                    <li>create the course calendar, and course information, for insertion in the newsletter</li>
                    <li>create a list of members who have opted to receive their newsletters by post, in a format suitable for the printers</li>
                    <li>display interesting statistics</li>
                    <li>print the rolls for each class</li>
                    <li>print the class contact details for each facilitator</li>
                    <li>re-enrol</li>
                    <li>enter details on courses, venues, members, etc</li>
                    <li>download and print commonly used forms (such as blank rolls, accident report forms)</li>
                </ul>
                
                
            </div>
        </div>
    </div>
@endsection
