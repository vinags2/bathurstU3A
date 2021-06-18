@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Still Under Development'])
        <div class="row mt-2">
            <div>
                <p>The page/report you have requested is still under development</p>

                <p>If the information you are seeking is urgent please contact the <a href="mailto:webadministator@bathurstu3a.com">
                database administrator</a>.</p>
                
                <p>Otherwise, please be patient. The database is being developed by a volunteer in his spare time.</p>

            </div>
        </div>
    </div>
@endsection
