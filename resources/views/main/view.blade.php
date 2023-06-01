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
                        <option value="{{$k}}">{{$v}}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div id="intradaily-exercises" class="intradaily-exercises-common mt-4">

            <div class="row mb-2">
                <div class="col-05-cstm text-start">#</div>
                <div class="col-35-cstm text-start">упраж&shyнение</div>
                <div class="col-2 text-start">повто&shyрений</div>
                <div class="col-5 text-start"><span class="">комментарий</span></div>
                <div class="col-1"></div>
            </div>

            <div id="intradaily-exercises-body">

                @foreach($user_physical_exercises as $user_physical_exercise)
                    <div draggable="true" class="row block-body">
                        <div class="col-05-cstm text-start physical-exercise-{{$user_physical_exercise['id']}}">
                            {{$user_physical_exercise['intraday_key']}}
                        </div>
                        <div class="col-35-cstm text-start physical-exercise-{{$user_physical_exercise['id']}}">
                            <span>{{$user_physical_exercise['physical_exercises']['name']}}</span>
                        </div>
                        <div class="col-2">
                            <div class="border-bottom-hover border-bottom">
                                <input type="text" value="{{ $user_physical_exercise['count'] }}"
                                       name="pe-count-{{$user_physical_exercise['id']}}"
                                       class="item-count" autocomplete="none">
                            </div>
                        </div>

                        <div class="col-5">
                            <div class="border-bottom-hover border-bottom">
                                <input title="{{ $user_physical_exercise['comment'] }}" type="text"
                                       value="{{ $user_physical_exercise['comment'] }}"
                                       name="pe-comment-{{$user_physical_exercise['id']}}"
                                       class="item-comment"
                                       autocomplete="none">
                            </div>
                        </div>
                        <div class="col-1 delete-control">
                            <div class="h5 position-relative">
                                <i id="i-element-{{$user_physical_exercise['id']}}"
                                   class="bi bi-x position-absolute end-0"></i>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>


        <div id="intradaily-exercises-low-res" class="intradaily-exercises-common mt-4">
            <div class="row mt-4">
                <div class="col-7">
                    <div class="text-start">
                        <span class="color-goldenrod">Название</span>
                    </div>
                </div>
                <div class="col-1 ie-line-balancer">
                    &nbsp
                </div>
                <div class="col-2">
                    <div class="header-images">
                        <div>
                            <img src="{{ Vite::asset('resources/images/icons/counter2.png') }}"
                                 alt="количество повторений"/>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="header-images">
                        <div>
                            <img src="{{ Vite::asset('resources/images/icons/action2.png') }}" alt="действие"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="border-bottom">
                        <span class="color-gray">Комментарий</span>
                    </div>
                </div>
            </div>

            <div id="intradaily-exercises-low-res-body">

                @foreach($user_physical_exercises as $user_physical_exercise)
                    <div draggable="true" class="row block-body mt-4">
                        <div class="col-8">
                            <div class="text-start physical-exercise-{{$user_physical_exercise['id']}}">
                                <span class="color-goldenrod">
                                    {{$user_physical_exercise['physical_exercises']['name']}}
                                </span>
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="border-bottom-hover border-bottom">
                                <input type="text" value="{{ $user_physical_exercise['count'] }}"
                                       name="pe-count-{{$user_physical_exercise['id']}}"
                                       class="item-count text-center" autocomplete="none">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="delete-control text-center">
                                <i id="i-element-{{$user_physical_exercise['id']}}"
                                   class="bi bi-x "></i>
                            </div>
                        </div>

                    </div>

                    <div class="row block-body">
                        <div class="col-12">
                            <div class="border-bottom-hover border-bottom">
                                <span class="color-gray" contenteditable="true"
                                      id="editor-{{$user_physical_exercise['intraday_key']}}"
                                      data-title="{{ $user_physical_exercise['comment'] }}">
                                      {!! $user_physical_exercise['comment'] ?? '&nbsp;' !!}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        <div class="mt-4 pb-2">
            {{ $user_physical_exercises->onEachSide(1)->links()  }}
        </div>
    </div>

@endsection
