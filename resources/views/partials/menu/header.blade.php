  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', config('app.name'))</title>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('dist/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/bootstrap-4-navbar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/global.css') }}">