@extends('layouts.app')
@section('content')

    <div id="calendar">
        <div class="text-center @if(request('device_type') != 'computer') pt-2 @endif">
            <strong>{{$month_name}}, {{$year}}</strong>
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
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="vert">
        vert
    </div>
    <div class="hory">
        hory
    </div>

@endsection
