@extends('layouts.app')
@section('content')

    <div id="calendar">
        <div class="text-center @if(request('device_type') != 'computer') pt-2 @endif">
            <input type="text" id="month-picker" readonly class="month-picker cursor-pointer"
                   value="{{$month_name}} {{$year}}">

            <div id="calendar-component" class="jcalendar jcalendar-container">
                <div class="jcalendar-container" style="top: 2px; left: 0px;">
                    <div id="calendar-content" class="jcalendar-content">
                        <div class="jcalendar-table">
                            <table cellpadding="0" cellspacing="0">
                                <thead>
                                <tr>
                                    <td colspan="2" class="jcalendar-prev"></td>
                                    <td class="jcalendar-header" colspan="3"><span
                                            class="jcalendar-month">{{getMonthNameLoc($month)}}</span><span
                                            class="jcalendar-year">{{$year}}</span></td>
                                    <td colspan="2" class="jcalendar-next"></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="7">
                                        <table class="jcalendar-months" width="100%">
                                            @php
                                                $k = 0;
                                            @endphp
                                            @for($i = 1; $i <= 3; $i++)
                                                <tr align="center">
                                                    @for($j = 1; $j <= 4; $j++)
                                                        @php
                                                            $k++;
                                                        @endphp
                                                        <td class="jcalendar-set-month @if(Carbon\Carbon::now()->month($month)->month === $k) jcalendar-selected @endif"
                                                            data-value="{{$k}}">{{$month_range[$i][$j]}}</td>
                                                    @endfor
                                                </tr>
                                            @endfor
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="jcalendar-controls">
                            <div style="flex-grow: 10;">
                                <button type="button" class="jcalendar-update">Перейти</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jcalendar-backdrop"></div>
            </div>

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

    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            let monthsList = {!! json_encode($months_list) !!};
            let monthNumber = {!! json_encode($month) !!};
            let calendarComponent = document.getElementById('calendar-component');
            let jcalendarYearElem = document.getElementsByClassName('jcalendar-year')[0];

            if (getWindowWidth() <= 800) {
                calendarComponent.classList.add('jcalendar-fullsize');
            }

            window.addEventListener('click', function (e) {
                if (document.getElementById('month-picker').contains(e.target)) {
                    if (calendarComponent.classList.contains('jcalendar-focus') === false) {
                        calendarComponent.classList.add('jcalendar-focus');
                    }
                }

                if (document.getElementById('calendar-content').contains(e.target) === false && document.getElementById('month-picker').contains(e.target) === false) {
                    if (calendarComponent.classList.contains('jcalendar-focus')) {
                        calendarComponent.classList.remove('jcalendar-focus');
                    }
                }

                if (document.getElementsByClassName('jcalendar-prev')[0]?.contains(e.target)) {
                    let year = jcalendarYearElem?.innerHTML;
                    jcalendarYearElem.innerHTML = year - 1;
                }

                if (document.getElementsByClassName('jcalendar-next')[0]?.contains(e.target)) {
                    let year = jcalendarYearElem?.innerHTML;
                    jcalendarYearElem.innerHTML = parseInt(year) + 1;
                }

                if (document.querySelector('#calendar-content .jcalendar-months tbody').contains(e.target)) {
                    let jcalendarSetMonthObjects = document.getElementsByClassName("jcalendar-set-month");
                    for (const [key, value] of Object.entries(jcalendarSetMonthObjects)) {
                        if (value.contains(e.target)) {
                            document.getElementsByClassName("jcalendar-month")[0].innerHTML = monthsList[value.getAttribute('data-value') - 1].substr(0, 3);
                            monthNumber = value.getAttribute('data-value') < 10 ? '0' + value.getAttribute('data-value') : value.getAttribute('data-value');
                            value.classList.add('jcalendar-selected');
                        } else {
                            value.classList.remove('jcalendar-selected');
                        }
                    }
                }

                if (document.getElementsByClassName('jcalendar-update')[0]?.contains(e.target)) {
                    window.location.replace(window.location.origin + '?timespan=' + jcalendarYearElem.innerHTML + '-' + monthNumber);
                }

            });
        });

        getWindowWidth = function () {
            let y = window, E = document, h = E.documentElement, d = E.getElementsByTagName("body")[0],
                L = y.innerWidth || h.clientWidth || d.clientWidth;
            return L
        }
    </script>

@endsection
