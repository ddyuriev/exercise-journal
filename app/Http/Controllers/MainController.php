<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{

    /**
     * MainController constructor.
     * @param CalendarService $calendarService
     */
    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'year-month' => 'date_format:Y-m',
        ]);
        $inputDate = $request->input('year-month');
        $date = $inputDate ? Carbon::parse($inputDate)->locale('ru_RU') : Carbon::now()->locale('ru_RU');
        return view('main.index', [
            'year' => $date->year,
            'month' => $date->format('m'),
            'month_name' => $date->monthName,
            'day' => $date->format('d'),
            'calendar' => $this->calendarService->getCalendar($date)
        ]);
    }
}
