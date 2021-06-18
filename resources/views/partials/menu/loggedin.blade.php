<div class="collapse navbar-collapse" id="navbarsExampleDefault">

    <ul class="navbar-nav mr-auto">
        
        @foreach ($menus as $outermenu)
        @foreach ($outermenu as $menu)
            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle" href="#" id="menu" data-toggle="dropdown">{{ $menu['name'] }}</a>
                @if ($menu['submenus'] > 0)
                
                    <ul class="dropdown-menu">

                        @for ($i = 0; $i < $menu['submenus']; $i++)
                            <li>
                                <a class="dropdown-item nav-link"
                                {{-- <a class="dropdown-item nav-link" --}}
                                    @if ($menu[$i][$i]['newPage'])
                                        target="_blank"
                                    @endif
                                    href="{{ $menu[$i][$i]['url'] }}">{{ $menu[$i][$i]['name'] }}
                                </a>
                            </li>
                            
                        @endfor
                    </ul>
                    
                @endif
                
            </li>
        
        @endforeach
        @endforeach
        
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('logout') }}">Logout</a>
        </li>
        
    </ul>

</div>