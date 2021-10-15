@extends('layouts.menu')
 
@section('content')
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script> -->
<script src = "{{ asset('js/vue.js') }}"></script>
<script src = "{{ asset('js/lodash.min.js') }}"></script>
<script src = "{{ asset('js/axios.min.js') }}"></script>
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])

        <div class="container" id="checkboxtest">
              <input type="checkbox" class="mr-1" title="click to test" @change="setallyear()"
                   name="checkboxterm[0]"
                   v-model="checkboxterm[0]"
                   value="1"
              />
              <label class="mr-3" title="click to test"> Term 1 </label>
               <input type="checkbox" class="mr-1" title="click to test" @change="setallyear()"
                   name="checkboxterm[1]"
                   v-model="checkboxterm[1]"
                   value="1"
               />
               <label class="mr-3" title="click to test"> Term 2 </label>
               <input type="checkbox" class="mr-1" @change="setallyear(true)" title="click to test" 
                   name="allyear"
                   v-model="allyr"
                   value="1"
               />
               <label class="mr-3" title="click to test"> All year </label>
        </div>
    </div>
        <script>
            var app2 = new Vue({
            el: '#checkboxtest',
            data: {
              checkboxterm: [],
              allyr: false
            },
            created: function () {
              for (let i=0; i < 2; i++) {
                this.checkboxterm[i] = true;
              }
            },
            methods: {
                setallyear: function(isallyear = false) {
                  if (isallyear) {
                    if (this.allyr) {
                      this.setcheckboxes();
                    } else {
                      this.setcheckboxes(true);
                    }
                  } else {
                    this.allyr = !this.checkboxterm[0] & !this.checkboxterm[1];
                  }
                },
                setcheckboxes(torf = false) {
                  this.checkboxterm.forEach((i,key) => {
                    this.checkboxterm[key] = torf;
                  });
                }
            }
            })
        </script>
@endsection
