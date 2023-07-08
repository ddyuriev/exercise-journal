@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-6">

            <form method="POST" class="" action="{{ route('settings.physical-exercises.store') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Название" value="{{old('name')}}">
                    @error('name')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <textarea name="description" class="form-control" placeholder="Описание"></textarea>
                </div>
                <div class="mb-3 text-left">
                    <button type="submit" class="btn btn-primary w-100">Создать</button>
                </div>
            </form>
        </div>
    </div>

@endsection
