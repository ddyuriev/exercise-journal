<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Services\UserPhysicalExerciseService;

class StatisticsController extends Controller
{
    /**
     * @var UserPhysicalExerciseService
     */
    private $userPhysicalExerciseService;

    /**
     * StatisticsController constructor.
     * @param UserPhysicalExerciseService $userPhysicalExerciseService
     */
    public function __construct(UserPhysicalExerciseService $userPhysicalExerciseService)
    {
        $this->userPhysicalExerciseService = $userPhysicalExerciseService;
    }

    /**
     * @param StatisticsRequest $request
     * @return array|bool[]|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
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
