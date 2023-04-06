<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\CreateUserPhysicalExercisesRequest;
use App\Http\Requests\DestroyUserPhysicalExercisesRequest;
use App\Http\Requests\UpdateUserPhysicalExercisesRequest;
use App\Models\UserPhysicalExercise;
use App\Services\UserPhysicalExerciseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPhysicalExerciseController extends Controller
{

    private $date;

    private $perPage;

    public function __construct(Request $request, UserPhysicalExerciseService $userPhysicalExerciseService)
    {
        $data = $request->all();
        if (!empty($data['date'])) {
            $this->date = Carbon::parse($data['date']);
        }
        $this->perPage = config('pagination.settings.per_page');
        $this->userPhysicalExerciseService = $userPhysicalExerciseService;
    }

    /**
     * @param $date
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($date, Request $request)
    {
        $physicalExercises = Auth::user()
            ->with('physicalExercises')
            ->first()
            ->physicalExercises
            ->pluck('name', 'id');

        $physicalExercises->prepend('выберите упражнение');
        $date = Carbon::parse($date);

        $userPhysicalExercises = UserPhysicalExercise
            ::with('physical_exercises')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->paginate($this->perPage);

        return view('main.view', [
            'device_type' => $request->device_type,
            'year' => $date->year,
            'month_name' => $date->monthName,
            'day' => $date->format('d'),
            'physical_exercises' => $physicalExercises,
            'user_physical_exercises' => $userPhysicalExercises,
        ]);
    }

    /**
     * @param CreateUserPhysicalExercisesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateUserPhysicalExercisesRequest $request)
    {
        $data = $request->all();

        $createData = [
            'user_id' => Auth::id(),
            'physical_exercise_id' => $data['physicalExerciseId'],
            'intraday_key' => $this->calculateNewIntradayKey($this->date),
        ];
        if ($this->date->clone()->toDateString() <= Carbon::now()->toDateString()) {
            $createData['created_at'] = $this->date->clone()->endOfDay();
        } elseif ($this->date->clone()->toDateString() >= Carbon::now()->toDateString()) {
            $createData['created_at'] = $this->date->clone()->startOfDay();
        }

        UserPhysicalExercise::create($createData);

        $parsedQueryStringArr = StringHelper::parseQueryString($data['queryString']);
        $page = !empty($parsedQueryStringArr['page']) ? $parsedQueryStringArr['page'] : 1;

        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $page);

        return response()->json([
            'is_success' => true,
            'is_need_reload' => $this->getRemainder() % $this->perPage === 1,
            'items' => $userPhysicalExercises,
        ]);

    }

    /**
     * @param $id
     * @param UpdateUserPhysicalExercisesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateUserPhysicalExercisesRequest $request)
    {
        $data = $request->all();

        $insertData = [];
        if (array_key_exists('count', $data)) {
            $insertData['count'] = $data['count'];
        }
        if (array_key_exists('comment', $data)) {
            $insertData['comment'] = $data['comment'];
        }

        UserPhysicalExercise::where('id', $id)
            ->where('user_id', Auth::id())
            ->update($insertData);

        $parsedQueryStringArr = StringHelper::parseQueryString($data['queryString']);
        $page = !empty($parsedQueryStringArr['page']) ? $parsedQueryStringArr['page'] : 1;

        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $page);

        return response()->json([
            'is_success' => true,
            'items' => $userPhysicalExercises,
        ]);

    }

    /**
     * @param $id
     * @param DestroyUserPhysicalExercisesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, DestroyUserPhysicalExercisesRequest $request)
    {
        $data = $request->all();

        $userPhysicalExercises = UserPhysicalExercise::where('user_id', Auth::id())
            ->where('id', $id)->first();

        if (empty($userPhysicalExercises)) {
            return response()->json([
                'is_success' => false
            ]);
        }
        try {
            $userPhysicalExercises->delete();
        } catch (\Throwable $exception) {
            return response()->json([
                'is_success' => false,
            ]);
        }
        $this->updateIntradayKeys($this->date);

        $parsedQueryStringArr = StringHelper::parseQueryString($data['queryString']);
        $page = !empty($parsedQueryStringArr['page']) ? $parsedQueryStringArr['page'] : 1;

        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $page);

        return response()->json([
            'is_success' => true,
            'is_need_reload' => $this->getRemainder() % $this->perPage === 0,
            'items' => $userPhysicalExercises
        ]);
    }


    /**
     * @param $date
     * @return int
     */
    private function calculateNewIntradayKey($date): int
    {
        $maxIntradayKey = UserPhysicalExercise::where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->max('intraday_key');

        return $maxIntradayKey ? $maxIntradayKey + 1 : 1;
    }

    /**
     * @param $date
     */
    private function updateIntradayKeys($date)
    {
        $currentKeys = UserPhysicalExercise::where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->orderBy('id')
            ->pluck('intraday_key', 'id')
            ->toArray();

        $currentKeysIds = array_keys($currentKeys);
        $currentKeysIdsStr = implode(',', $currentKeysIds);
        $caseStr = '';
        foreach ($currentKeysIds as $key => $value) {
            $intradayKey = $key + 1;
            $caseStr .= " WHEN id = {$value} THEN {$intradayKey}";
        }

        DB::update("UPDATE user_physical_exercises SET intraday_key =
                        (CASE
                            $caseStr
                         END)
        WHERE id IN ($currentKeysIdsStr)");
    }

    /**
     * @return int
     */
    private function getRemainder(): int
    {
        return UserPhysicalExercise
            ::with('physical_exercises')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $this->date->startOfDay())
            ->where('created_at', '<=', $this->date->clone()->endOfDay())
            ->count();
    }

}
