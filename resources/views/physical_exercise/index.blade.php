@extends('layouts.app')
@section('content')

    <div id="physical-exercises-settings">
        <div class="row physical-exercises-settings-header">
            <div class="col-9 d-flex align-items-center mb-3 justify-content-center">
                {{--                <div class="align-middle">Выбранные упражнения<span class="fs-2"></span></div>--}}
                Выбранные упражнения
            </div>
            <div class="col-3 d-flex align-items-center mb-3 flex-row-reverse">
                <a href="{{ route('settings.physical-exercises.create') }}"> <i class="bi bi-plus-lg fs-2"></i></a>

                {{--                <a class="btn btn-grow" href="{{ route('merchant.new') }}" role="button">{{__('Add New')}}</a>--}}
            </div>
        </div>

        <div class="col-md-4 mt-2">
            <div class="row">
                <div class="col-sm-12">
                    <input type="text" name="search" class="form-control search-input" value="{{request('name') }}"
                           placeholder="{{__('Search')}}"/>
                </div>
            </div>
        </div>

        <table class="mt-2 table table-borderless table-hover">
            <thead>
            <tr>
                <td>Название</td>
                <td>Описание</td>
                <td>Действие</td>
            </tr>
            </thead>
            <tbody>
            @foreach($physical_exercises as $physical_exercise)
                <tr id="tr-{{$physical_exercise['id']}}"
                    class="bg-gradient @if(!$physical_exercise['active']) physical-exercises-unselected @endif">
                    <td>{{$physical_exercise['name']}}</td>
                    <td>{{$physical_exercise['description']}}</td>
                    <td class="action-icons text-center">
                        <form method="POST" id="pe-toggle-{{$physical_exercise['id']}}"
                              class="form-physical-exercises-toggle"
                              action="{{ route('settings.physical-exercises.toggle') }}">
                            <button class="btn btn-grow btn-confirm-recalculate">
                                <i class="@if($physical_exercise['active']) bi bi-toggle2-on @else  bi bi-toggle2-off @endif"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $physical_exercises->appends(\Request::except('device_type'))->onEachSide(1)->links() }}
    </div>

@endsection
