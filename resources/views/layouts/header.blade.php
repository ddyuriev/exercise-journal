<header>

    @if(request('device_type') == 'computer')
        <div class="header-desktop">
            <div class="container">

                <div class="row">

                    <div class="col-1">
                        @if (request('device_type') == 'computer')

                            <div class="container-right p-4 mb-3">
                                <div class="block-login">
                                    <div class="row mt-0">
                                        <div class="col main-navigation text-center">
                                            <a class="nav-link text-center" href="{{route('main.index')}}">
                                                <i class="bi bi-house-door"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-9">
                    </div>
                    <div class="col-2">
                        @if (request('device_type') == 'computer')
                            @auth
                                <div class="container-right p-4 mb-3">
                                    <div class="block-login">
                                        <div class="row mt-0">
                                            <div class="col main-navigation text-center">
                                                <i id="profile-icon" class="bi bi-person cursor-pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        @endif
                    </div>

                </div>

                <div id="pc-profile-widget" class="profile-widget">

                    <div class="@if (request('device_type') == 'computer') row @endif">

                        @if (request('device_type') == 'computer')
                            <div class="col">
                                @auth
                                    <div class="container-right p-4 mb-3">
                                        <div class="block-login">
                                            <div class="row mt-0">
                                                <div class="col main-navigation">
                                                    <a class="nav-link text-center mt-4"
                                                       href="{{route('profile.index')}}">
                                                        <i class="bi bi-person"></i>
                                                    </a>
                                                    <a class="nav-link text-center mt-4"
                                                       href="{{route('settings.index')}}">
                                                        <i class="bi bi-gear"></i>
                                                    </a>
                                                    <form method="POST" name="logout" class="text-center mt-4"
                                                          action="{{ route('logout') }}">
                                                        @csrf
                                                        <span role="button" onclick="logout.submit()">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </span>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        @endif

                    </div>


                </div>

            </div>
        </div>
    @endif

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
