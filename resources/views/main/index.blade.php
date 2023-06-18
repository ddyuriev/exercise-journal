@extends('layouts.app')
@section('content')

    <div id="calendar">
        <div class="text-center @if(request('device_type') != 'computer') pt-2 @endif">
            <input type="month" class="month-picker" name="start" min="2022-01" value="{{$year}}-{{$month}}">
        </div>
        <div class="row mb-2 mt-4">
            @foreach(['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'] as $item)
                <div class="col text-center">{{$item}}</div>
            @endforeach
        </div>
        <div id="calendar-body" class="calendar-table-body">
            @foreach($calendar as $keyWeek => $week)
                <div class="row">
                    @foreach($week as $keyDay => $itemDay)
                        <div
                            onclick="window.location.href='day/{{$year}}-{{$itemDay['month']}}-{{$itemDay['date']<10 ? '0' . $itemDay['date'] : $itemDay['date']}}'"
                            class="col {{$itemDay['week'] ?? ''}} {{$itemDay['day'] ?? ''}} @if($itemDay['is_current_date']) current_date @endif">
                            <div class="text-end" id="calendar-table-td-div-{{$itemDay['date']}}">
                                <span class="@if(!$itemDay['is_this_month']) nearby_month @endif">
                                    {{$itemDay['date']}}
                                </span>
                            </div>
                            @if(!empty($itemDay['exercises_count']))
                                <div class="daygrid-dot">
                                </div>
                                <div class="exercises_count">
                                    {{$itemDay['exercises_count']}}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

@endsection
