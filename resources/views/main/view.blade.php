@extends('layouts.app')
@section('content')


    <div class="pt-3">

        <div class="text-center">
            <strong>{{$month_name}} {{$day}}, {{$year}}</strong>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <select id="select-physical_exercise" name="physical_exercises[]" class="form-control select2">
                    @foreach($physical_exercises as $k => $v)
                        <option value="{{$k}}"
                                @if(in_array($k, request('provider') ?? [])) selected @endif>{{$v}}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div id="intradaily-exercises" class="mt-4">

            <div class="row mb-2">
                <div class="text-start sort-field">#</div>
                <div class="col-3 text-start">упраж&shyнение</div>
                <div class="col-2 text-start">повто&shyрений</div>
                <div class="col text-start"><span class="">комментарий</span></div>
                <div class="col-1"></div>
            </div>

            <div id="intradaily-exercises-body">

                @foreach($user_physical_exercises as $user_physical_exercise)
                    <div draggable="true" class="row block-body">
                        <div class="text-start sort-field physical-exercise-{{$user_physical_exercise['id']}}">
                            {{$user_physical_exercise['intraday_key']}}
                        </div>
                        <div class="col-3 text-start physical-exercise-{{$user_physical_exercise['id']}}">
                            <span>{{$user_physical_exercise['physical_exercises']['name']}}</span>
                        </div>
                        <div class="col-2">
                            <div class="input-parent border-bottom">
                                <input type="text" value="{{ $user_physical_exercise['count'] }}"
                                       name="pe-count-{{$user_physical_exercise['id']}}"
                                       class="item-count" autocomplete="none">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-parent border-bottom">
                                <input type="text" value="{{ $user_physical_exercise['comment'] }}"
                                       name="pe-comment-{{$user_physical_exercise['id']}}"
                                       class="item-comment"
                                       autocomplete="none">
                            </div>
                        </div>
                        <div class="col-1 delete-control">
                            <div class="w-50 h5" style="margin-left: -5%">
                                <i id="i-element-{{$user_physical_exercise['id']}}" class="bi bi-x"></i>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>

        <div class="mt-4 pb-2">
            {{ $user_physical_exercises->onEachSide(-1)->links()  }}
        </div>
    </div>

@endsection
