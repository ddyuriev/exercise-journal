<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Services\StatisticsService;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function __construct(private StatisticsService $statisticsService)
    {
    }

    public function index(StatisticsRequest $request): View
    {
        $statistics = $this->statisticsService->statistics($request->all()['period'] ?? 1);

        $resultData = ['data' => $statistics];

        if ($request->ajax()) {
            return response()->json(array_merge($resultData, ['is_success' => true]));
        } else {
            return view('statistics.index', $resultData);
        }
    }
}
