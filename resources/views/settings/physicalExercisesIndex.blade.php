@extends('layouts.app')
@section('content')

    <div id="physical-exercises-settings">
        <div class="text-center">
            <strong>Выбранные упражнения</strong>
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
                <tr id="tr-{{$physical_exercise['id']}}" class="bg-gradient @if(!$physical_exercise['users_count']) physical-exercises-unselected @endif">
                    <td>{{$physical_exercise['name']}}</td>
                    <td>{{$physical_exercise['description']}}</td>
                    <td class="action-icons text-center">
                        <form method="POST" id="pe-toggle-{{$physical_exercise['id']}}"  class="form-physical-exercises-toggle" action="{{ route('settings.physical-exercises.toggle') }}">
                            <button class="btn btn-grow btn-confirm-recalculate">
                                <i class="@if($physical_exercise['users_count']) bi bi-toggle2-on @else  bi bi-toggle2-off @endif"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $physical_exercises->onEachSide(1)->links() }}
    </div>

@endsection
