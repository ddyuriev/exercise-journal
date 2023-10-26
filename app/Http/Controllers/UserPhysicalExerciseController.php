<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\UserPhysicalExercises\StoreUserPhysicalExerciseRequest;
use App\Http\Requests\UserPhysicalExercises\DestroyUserPhysicalExerciseRequest;
use App\Http\Requests\UserPhysicalExercises\UpdateUserPhysicalExerciseRequest;
use App\Models\PhysicalExercise;
use App\Models\UserPhysicalExercise;
use App\Services\UserPhysicalExerciseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPhysicalExerciseController extends Controller
{

    /**
     * @var Carbon
     */
    private $date;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $perPage;

    /**
     * UserPhysicalExerciseController constructor.
     * @param Request $request
     * @param UserPhysicalExerciseService $userPhysicalExerciseService
     */
    public function __construct(Request $request, private UserPhysicalExerciseService $userPhysicalExerciseService)
    {
        $data = $request->all();
        if (!empty($data['date'])) {
            $this->date = Carbon::parse($data['date']);
        }
        $this->perPage = config('pagination.settings.per_page');
    }

    /**
     * @param $date
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($date, Request $request)
    {
        //the limit is due to the use of the list of items in select2 component
        $physicalExercises = PhysicalExercise::where('created_by', Auth::id())
            ->orWhere('status', PhysicalExercise::STATUS_APPROVED)
            ->take(env('USER_PHYSICAL_EXERCISES_LIMIT', 5000))
            ->pluck('name', 'id');

        $physicalExercises->prepend('выберите упражнение', 0);

        $date = Carbon::parse($date);

        $userPhysicalExercises = UserPhysicalExercise
            ::with('physical_exercises')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->paginate($this->perPage);

        return view('user_physical_exercise.view', [
            'device_type' => $request->device_type,
            'year' => $date->year,
            'month_name' => $date->monthName,
            'day' => $date->format('d'),
            'physical_exercises' => $physicalExercises,
            'user_physical_exercises' => $userPhysicalExercises,
        ]);
    }

    /**
     * @param StoreUserPhysicalExerciseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserPhysicalExerciseRequest $request)
    {
        $data = $request->all();

        $createData = [
            'user_id' => Auth::id(),
            'physical_exercise_id' => $data['physicalExerciseId'],
            'intraday_key' => $this->calculateNewIntradayKey($this->date),
        ];

        $itemsOldCount = $this->getRemainder();

        //special created_at if creation of records retroactively or in advance
        if ($this->date->clone()->toDateString() < Carbon::now()->toDateString()) {
            $createData['created_at'] = $this->date->clone()->endOfDay();
        } elseif ($this->date->clone()->toDateString() > Carbon::now()->toDateString()) {
            $createData['created_at'] = $this->date->clone()->startOfDay();
        }

        UserPhysicalExercise::create($createData);

        $queryStringParsedArr = StringHelper::httpQueryStringParser($data['queryString']);
        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $queryStringParsedArr['page']);

        $isNeedReload = $itemsOldCount !== 0 && ceil($itemsOldCount / $this->perPage) != ceil(($itemsOldCount + 1) / $this->perPage);
        return response()->json([
            'is_success' => true,
            'is_need_reload' => $isNeedReload,
            'page_correction' => $isNeedReload ? $queryStringParsedArr['page'] + 1 : 0,
            'items' => $userPhysicalExercises,
        ]);

    }

    /**
     * @param $id
     * @param UpdateUserPhysicalExerciseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateUserPhysicalExerciseRequest $request)
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

        $queryStringParsedArr = StringHelper::httpQueryStringParser($data['queryString']);
        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $queryStringParsedArr['page']);

        return response()->json([
            'is_success' => true,
            'items' => $userPhysicalExercises,
        ]);

    }

    /**
     * @param $id
     * @param DestroyUserPhysicalExerciseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, DestroyUserPhysicalExerciseRequest $request)
    {
        $data = $request->all();

        $userPhysicalExercise = UserPhysicalExercise::where('user_id', Auth::id())
            ->where('id', $id)->first();

        if (empty($userPhysicalExercise)) {
            return response()->json([
                'is_success' => false
            ]);
        }

        $itemsOldCount = $this->getRemainder();

        try {
            $userPhysicalExercise->delete();
        } catch (\Throwable $exception) {
            return response()->json([
                'is_success' => false,
            ]);
        }
        $this->updateIntradayKeys($this->date);

        $queryStringParsedArr = StringHelper::httpQueryStringParser($data['queryString']);
        $userPhysicalExercises = $this->userPhysicalExerciseService->getUserPhysicalExercises($this->date, $queryStringParsedArr['page']);

        $isNeedReload = $itemsOldCount !== 1 && ceil($itemsOldCount / $this->perPage) != ceil(($itemsOldCount - 1) / $this->perPage);
        return response()->json([
            'is_success' => true,
            'is_need_reload' => $isNeedReload,
            'page_correction' => $isNeedReload ? $queryStringParsedArr['page'] - 1 : 0,
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
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateIntradayKeys($date): JsonResponse
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
            /**
             * @psalm-suppress InvalidCast
             */
            $caseStr .= " WHEN id = {$value} THEN {$intradayKey}";
        }

        if ($caseStr && $currentKeysIdsStr) {
            try {
                DB::update("UPDATE user_physical_exercises SET intraday_key =
                        (CASE
                            $caseStr
                         END)
            WHERE id IN ($currentKeysIdsStr)");
            } catch (\Throwable $exception) {
                return response()->json([
                    'is_success' => false,
                ]);
            }
        }

        return response()->json([
            'is_success' => true,
        ]);
    }

    /**
     * @return int
     */
    private function getRemainder(): int
    {
        return UserPhysicalExercise
            ::with('physical_exercises')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $this->date->clone()->startOfDay())
            ->where('created_at', '<=', $this->date->clone()->endOfDay())
            ->count();
    }

}
