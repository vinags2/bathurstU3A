@extends('layouts.menu')

@section('content')
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Test Page'])

        <div id="app" v-bind:title="title">
            @{{ isInputDisabled ? message2 : message }}
            <span v-if="seen">Now you see me</span>
            <span v-else>Now you don't</span>
            @{{ reversedMessage }}
            <ol>
                <li v-for="todo in todos">
                    @{{ todo.text }}
                </li>
            </ol>
            <input v-model="message" v-bind:disabled="isInputDisabled">
        </div>
        <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
        <!-- Axios for AJAX calls, lodash for handy js utilities, especially debounce() -->
        <script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js"></script>
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                message: 'Hello Vue!',
                message2: 'Hello back to you!',
                title: 'This is the title',
                title2: 'You loaded this page on ' + new Date().toLocaleString(),
                seen: true,
                isInputDisabled: false,
                todos: [
                    { text: 'Learn JavaScript' },
                    { text: 'Learn Vue' },
                    { text: 'Build something awesome' }
                ]
            },
            computed: {
                reversedMessage: function () {
                return this.message.split('').reverse().join('')
            }
  }
        })
    </script>
@endsection
