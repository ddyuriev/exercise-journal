<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * @var CalendarService
     */
    private $calendarService;

    /**
     * MainController constructor.
     * @param CalendarService $calendarService
     */
    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $request->validate([
            'year-month' => 'date_format:Y-m',
        ]);
        $inputDate = $request->input('year-month');
        $date = $inputDate ? Carbon::parse($inputDate) : Carbon::now();
        return view('main.index', [
            'year' => $date->year,
            'month' => $date->format('m'),
            'month_name' => $date->monthName,
            'day' => $date->format('d'),
            'calendar' => $this->calendarService->getCalendar($date)
        ]);
    }
}
