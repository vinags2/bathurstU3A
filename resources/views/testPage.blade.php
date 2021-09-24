@extends('layouts.menu')
 
@section('content')
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script> -->
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])

        <div class="container">
  <div class="row">
    <div class="col-sm">
      One of three columns
    </div>
    <div class="col-sm">
      One of three columns
    </div>
    <div class="col-sm">
      One of three columns
    </div>
  </div>
</div>
    </div>
@endsection
