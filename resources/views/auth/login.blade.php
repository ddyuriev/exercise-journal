@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="container p-4 mb-4  d-flex justify-content-center @if(request('device_type') != 'computer') mt-4 @endif">
            <div class="col-4">
                <div class="block-login justify-content-center">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center">
                            <h4 class="mb-4">Вход</h4>
                        </div>
                        <div class="mb-3">
                            <input name="email" type="email" class="form-control" id="exampleInputEmail1"
                                   aria-describedby="emailHelp"
                                   placeholder="Логин">
                        </div>
                        <div class="mb-3">
                            <input name="password" type="password" class="form-control" id="exampleInputPassword1"
                                   placeholder="Пароль">
                        </div>

                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="vert">
        vert
    </div>
    <div class="hory">
        hory
    </div>


@endsection
