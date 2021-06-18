<!-- menu.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

  @include('partials.menu.header')

</head>
<body>
  <div class="container">
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      </button>
      <span class="navbar-brand">Bathurst U3A Database</span>
  
      @auth

        @include('partials.menu.loggedin')

      @else

        @include('partials.menu.notloggedin')

      @endauth

    </nav>
  </div>

  @yield('content')

  <footer class="page-footer ml-5 mt-5">
    © Copyright {{ date('Y') }} All Rights Reserved:
    <a href="https://www.gregvinall.com" target="_blank">Greg Vinall</a>
    for the <a href="https://www.bathurstu3a.com" target="_blank">Bathurst U3A</a>.
  </footer>

  @include('partials.menu.scripts')

</body>
</html>