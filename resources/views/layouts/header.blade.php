<header>

    @if(in_array(request('device_type'), ['phone', 'tablet']))
        <div class="header-mobile">
            <div class="container">
                <a href="{{route('main.index')}}" class="logo">EJ</a>

                <input class="side-menu" type="checkbox" id="side-menu"/>
                <label class="hamb" for="side-menu"><span class="hamb-line"></span></label>

                <div class="row mobile-menu-block">

                    <div class="col">
                        <nav class="nav">
                            <ul class="menu">
                                <li>
                                    <a class="nav-link" href="{{route('main.index')}}">
                                        главная
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="col">
                        <nav class="nav">
                            <ul class="menu">
                                <li class="">
                                    <div class="text-nowrap overflow-hidden" style="width: 30vw;">
                                        <a class="nav-link text-nowrap" href="{{route('profile.index')}}">
                                            @auth()
                                                {{ \Illuminate\Support\Facades\Auth::user()->name}}
                                            @endauth
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{route('settings.index')}}">
                                        настройки
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" class="" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="cursor-pointer"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            выход
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div>

            </div>
        </div>
    @endif

</header>
