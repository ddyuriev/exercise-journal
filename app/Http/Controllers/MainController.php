<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\IndexMainRequest;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * MainController constructor.
     * @param CalendarService $calendarService
     */
    public function __construct(private CalendarService $calendarService)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(IndexMainRequest $request)
    {
        $inputDate = $request->input('timespan');
        $date = $inputDate ? Carbon::parse($inputDate) : Carbon::now();

        return view('main.index', [
            'year' => $date->year,
            'month' => $date->format('m'),
            'month_name' => mb_convert_case($date->monthName, MB_CASE_TITLE, "UTF-8"),
            'day' => $date->format('d'),
            'calendar' => $this->calendarService->getCalendar($date),
            'month_range' => StringHelper::monthRange(),
            'months_list' => StringHelper::monthsList()
        ]);
    }
}
