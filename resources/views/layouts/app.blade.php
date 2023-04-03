<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css','resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body>

@include('layouts.header')

{{--<div class="pre-header">--}}
{{--    <div class="container">--}}
{{--    </div>--}}
{{--</div>--}}

<div class="container body-container">
    <div class="@if (request('device_type') == 'computer') row @endif">
        @if (request('device_type') == 'computer')
            <div class="col-1">
                @auth
                    <div class="container-right p-4 mb-4 sidebar-block_border">
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
                @endauth
            </div>
        @endif
        <div class="@if(request('device_type') == 'computer') col-9 @else pt-2 pb-4 @endif ">
            @yield('content')
        </div>
        @if (request('device_type') == 'computer')
            <div class="col-2">
                @auth
                    <div class="container-right p-4 mb-4 sidebar-block_border">
                        <div class="block-login">
                            <div class="row mt-0">
                                <div class="col main-navigation">
                                    <a class="nav-link text-center mt-2"
                                       href="{{route('profile.index')}}">
                                        <i class="bi bi-person"></i>
                                    </a>
                                    <a class="nav-link text-center mt-2"
                                       href="{{route('settings.index')}}">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                    <form method="POST" name="logout" class="text-center mt-2"
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


@if ($errors->any())
    @foreach($errors->all() as $error)
        <li>{{$error}}</li>
    @endforeach
@endif

</body>
</html>
