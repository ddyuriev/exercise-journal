<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\IndexMainRequest;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\View\View;


class MainController extends Controller
{
    public function __construct(private CalendarService $calendarService)
    {
    }

    public function index(IndexMainRequest $request): View
    {
        $inputDate = $request->input('timespan');
        $date = $inputDate ? Carbon::parse($inputDate) : Carbon::now();

        return view('main.index', [
            'year' => $date->year,
            'month' => intval($date->format('m')),
            'month_name' => mb_convert_case($date->monthName, MB_CASE_TITLE, "UTF-8"),
            'day' => $date->format('d'),
            'calendar' => $this->calendarService->getCalendar($date),
            'month_range' => StringHelper::monthRange(),
            'months_list' => StringHelper::monthsList()
        ]);
    }
}
