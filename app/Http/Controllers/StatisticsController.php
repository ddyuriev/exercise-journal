<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Services\UserPhysicalExerciseService;

class StatisticsController extends Controller
{

    /**
     * StatisticsController constructor.
     * @param UserPhysicalExerciseService $userPhysicalExerciseService
     */
    public function __construct(private UserPhysicalExerciseService $userPhysicalExerciseService)
    {
    }


    /**
     * @param StatisticsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(StatisticsRequest $request)
    {
        $statistics = $this->userPhysicalExerciseService->statistics($request->all()['period'] ?? 1);

        $resultData = ['data' => $statistics];

        if ($request->ajax()) {
            return response()->json(array_merge($resultData, ['is_success' => true]));
        } else {
            return view('statistics.index', $resultData);
        }
    }
}
