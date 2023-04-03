@extends('layouts.app')
@section('content')

    <div class="@if(request('device_type') == 'computer') mt-4 @endif">

        <nav class="nav">
            <ul class="menu">
                <li class="text-center">
                    <a class="nav-link" href="{{route('settings.physical-exercises.index')}}">
                        physical exercises
                    </a>
                </li>

            </ul>
        </nav>

    </div>

@endsection
