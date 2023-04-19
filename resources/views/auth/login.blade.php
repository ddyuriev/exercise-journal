@extends('layouts.app')
@section('content')

    <div class="row">
        <div
            class="container p-4 mb-4 d-flex justify-content-center @if(request('device_type') != 'computer') mt-4 @endif">
            <div class="col-4">
                <div class="block-login justify-content-center position-relative">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center">
                            <h4 class="mb-4">Вход</h4>
                        </div>
                        <div class="mb-3">
                            <input name="email" type="email" class="form-control" aria-describedby="emailHelp"
                                   placeholder="Логин">
                        </div>
                        <div class="mb-3">
                            <input name="password" type="password" class="form-control" placeholder="Пароль">
                        </div>

                        <div class="mb-3 text-left">
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </div>
                    </form>

{{--                    <div class="text-left position-absolute bottom-0 end-0">--}}
{{--                        <button type="submit" class="btn btn-light" onclick="window.location.href='register'">--}}
{{--                            Регистрация--}}
{{--                        </button>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>

@endsection
