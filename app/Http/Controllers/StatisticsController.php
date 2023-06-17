<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Services\UserPhysicalExerciseService;

class StatisticsController extends Controller
{
    public function __construct(UserPhysicalExerciseService $userPhysicalExerciseService)
    {
        $this->userPhysicalExerciseService = $userPhysicalExerciseService;
    }

    public function index(StatisticsRequest $request)
    {
        $statistics = $this->userPhysicalExerciseService->statistics($request->all()['period'] ?? 1);

        $resultData = ['data' => $statistics];

        if ($request->ajax()) {
            return array_merge($resultData, ['is_success' => true]);
        } else {
            return view('statistics.index', $resultData);
        }
    }
}
