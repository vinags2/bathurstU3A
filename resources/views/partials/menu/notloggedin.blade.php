<ul class="navbar-nav mr-auto">

    @if (Route::has('login'))
    
        @auth
        
            <li class="nav-item">
                <form action="{{ url('/') }}" method="POST" class="form-inline my-2 my-lg-0">
                    <button class="btn btn-outline-success my-2 my-sm-0" name="home" type="submit">Home</button>
                </form>
            </li>
            
        @else
        
            <li class="nav-item">
                <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('login') }}">Login</a>
            </li>
            
            @if (Route::has('register') && !config('myconfig.hideRegister'))
            
                <li class="nav-item">
                    <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('register') }}">Register</a>
                </li>
            
            @endif
            
            @if (Route::has('join') && !config('myconfig.hideJoin'))
            
                <li class="nav-item">
                    <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('join') }}">Join</a>
                </li>
            
            @endif
            
        @endauth
    
    @endif
    
</ul>