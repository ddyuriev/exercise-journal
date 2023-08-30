@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <form method="POST" class="" action="{{ route('settings.physical-exercises.update', $physical_exercise->id) }}">
                @method('PUT')
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Название" value="{{old('name', $physical_exercise->name)}}">
                    @error('name')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <textarea name="description" class="form-control"
                              placeholder="Описание">{{old('description', $physical_exercise->description)}}</textarea>
                </div>

                <div class="mb-3">
                    <select required name="status"
                            class="form-control select2 without-search @error('status') is-invalid @enderror">
                        <option value="" disabled selected>Статус</option>
                        <option value="1" @if(old('status', $physical_exercise->status) == 1) selected @endif>Приватное</option>
                        <option value="2" @if(old('status', $physical_exercise->status) == 2) selected @endif>Общее</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>

                <div class="mb-3 text-left">
                    <button type="submit" class="btn btn-primary w-100">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

@endsection
