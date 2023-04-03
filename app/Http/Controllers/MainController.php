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
        $now = Carbon::now()->locale('ru_RU');
        return view('main.index', [
            'year' => $now->year,
            'month_name' => $now->monthName,
            'day' => $now->format('d'),
            'calendar' => $this->calendarService->getCalendar()
        ]);
    }
}
