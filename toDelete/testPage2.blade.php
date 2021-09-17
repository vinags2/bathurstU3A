@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])
        <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
        <!-- Axios for AJAX calls, lodash for handy js utilities, especially debounce() -->
        <script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script>

        <div id="courseId">
            <p>
                Course:
                <input autofocus v-model="course">
            </p>
            <p>
                <select v-model="selectedCourse" v-if="seenAsWell" id="name_matches"  size="10" name="courseId" class="custom-select" >
                    <option disabled value="">If the name is in the list, please select it...</option>
                    <option v-for="course in courses" v-bind:value="course.id">@{{ course.name }}</option>
                </select>
            </p>
            <p v-if="seen">@{{ prompt }}</p>
        </div>

        <script>
            var app = new Vue({
            el: '#courseId',
            data: {
                course: '',
                initialPrompt: 'Enter a course name, or part thereof, and press RETURN',
                prompt: '',
                courses: [],
                selectedCourse: '',
                seen: true,
                seenAsWell: false
            },
            watch: {
                // whenever course changes, this function will run
                course: function (newCourse, oldCourse) {
                this.debouncedGetCourse()
                }
            },
            created: function () {
                // Limit how often we access the api, waiting until the user has completely
                // finished typing before making the ajax request.
                this.debouncedGetCourse = _.debounce(this.getCourse, 500)
                this.prompt = this.initialPrompt
            },
            methods: {
                getCourse: function () {
                    if (this.course === '') {
                        this.prompt = this.initialPrompt
                        this.seen = true
                        this.seenAsWell = false
                    } else {
                        this.prompt = 'Retrieving matches...'
                        this.seen = true
                        var sc = this
                        axios({
                            method: 'get',
                            url: '{{ url('coursesearch') }}',
                            params: {
                                name: sc.course
                            },
                            timeout: 2000
                        })
                            .then(function (response) {
                                sc.courses = response.data
                                sc.seen = false
                                sc.seenAsWell = true
                            })
                            .catch(function (error) {
                                sc.prompt = 'Error! Unable to retrieve matches. '
                                console.log(error)
                            })
                    }
                }
            }
            })
        </script>


@endsection
