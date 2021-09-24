@extends('layouts.menu')
 
@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])


<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script>


<div id = "course">
    <singleentry model="course" allownewmodels="true" ajaxurl="{{ url('coursesearch') }}"></singleentry>
</div>
<div id = "venue">
    <singleentry model="venue" ajaxurl='http://127.0.0.1/~gregvinall/bathurstu3a/db21/index.php/venuesearch'></singleentry>
</div>
<div id = "address">
    <singleentry model="address" ajaxurl='http://127.0.0.1/~gregvinall/bathurstu3a/db21/index.php/addresssearch' searchparameterkey='address'></singleentry>
</div>
<div id = "name">
    <singleentry model="member" ajaxurl='http://127.0.0.1/~gregvinall/bathurstu3a/db21/index.php/onelineclosenamesearch'></singleentry>
</div>
<script src = "{{ asset('js/singleentry.js') }}"></script>

<script>
var vm = new Vue({
   el: '#course'
});
var vm1 = new Vue({
   el: '#venue'
});
var vm2 = new Vue({
   el: '#address'
});
var vm3 = new Vue({
   el: '#name'
});
</script>

    </div>
@endsection
