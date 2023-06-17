<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css','resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body class="@if (request('device_type') == 'computer') type-computer @else type-mobile @endif">

@include('layouts.header')

<div class="container body-container pt-4">
    <div class="@if (request('device_type') == 'computer') row @endif">
        @if (request('device_type') == 'computer')
            <div class="col-1 col-left">
            </div>
        @endif
        <div class="@if(request('device_type') == 'computer') col-10-cstm @else pt-2 pb-4 @endif ">
            @yield('content')
        </div>
        @if (request('device_type') == 'computer')
            <div class="col-1 col-right">
            </div>
        @endif
    </div>
</div>

</body>
</html>
