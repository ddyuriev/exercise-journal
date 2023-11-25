@extends('layouts.app')
@section('content')

    <div id="physical-exercises-settings">
        <div class="row physical-exercises-settings-header">
            <div class="col-9 d-flex align-items-center mb-3 justify-content-center">
                Выбранные упражнения
            </div>
            <div
                class="col-3 d-flex align-items-center mb-3 flex-row-reverse @if(request('device_type') === 'computer') mt-2 @else mt-1 @endif">
                <a href="{{ route('settings.physical-exercises.create') }}"> <i class="bi bi-plus-lg fs-2"></i></a>
            </div>
        </div>

        <div class="col-md-4 mt-2">
            <div class="row">
                <div class="col-sm-12">
                    <input type="text" name="search" class="form-control search-input" value="{{request('search_query') }}"
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
                    class="@if(!$physical_exercise['user_id']) physical-exercises-unselected @endif">
                    <td class="@if ($physical_exercise['status'] == PhysicalExercise::STATUS_APPROVED) name-confirmed @endif">

                        @if(in_array($physical_exercise['status'], [PhysicalExercise::STATUS_PRIVATE, PhysicalExercise::STATUS_IN_MODERATION]) )
                            <a href="{{ route('settings.physical-exercises.edit', $physical_exercise['id']) }}">
                                {{ $physical_exercise['name']}}
                            </a>
                        @else
                            <a href="{{ route('settings.physical-exercises.show', $physical_exercise['id']) }}">
                                {{ $physical_exercise['name']}}
                            </a>
                        @endif
                    </td>
                    <td>{{$physical_exercise['description']}}</td>
                    <td class="action-icons text-center">
                        <form id="pe-toggle-{{$physical_exercise['id']}}"
                              class="form-physical-exercises-toggle float-start"
                              action="{{ route('settings.physical-exercises.toggle') }}">
                            <button class="btn btn-grow btn-confirm-recalculate">
                                <i class="@if($physical_exercise['user_id']) bi bi-toggle2-on @else  bi bi-toggle2-off @endif"></i>
                            </button>
                        </form>
                        @if($physical_exercise['created_by'] == Auth()->id() && in_array($physical_exercise['status'], statusesDifferentFromApproved()) )
                            <form id="pe-delete-{{$physical_exercise['id']}}"
                                  class="form-physical-exercises-delete float-start"
                                  action="{{ route('settings.physical-exercises.destroy', $physical_exercise['id']) }}">
                                <button class="btn btn-grow btn-confirm-recalculate">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $physical_exercises->appends(\Request::except('device_type'))->onEachSide(1)->links() }}
    </div>

@endsection
