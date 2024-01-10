<?php

namespace App\Services;

use App\Helpers\StringHelper;
use App\Models\User;
use App\Models\UserPhysicalExercise;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserPhysicalExerciseService
{

    /**
     * @param Carbon $date
     * @param int $page
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserPhysicalExercises(Carbon $date, int $page)
    {
        $perPage = config('pagination.settings.per_page');
        $skip = ($page - 1) * $perPage;

        return UserPhysicalExercise
            ::with('physical_exercises')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->skip($skip)
            ->take($perPage)
            ->get();
    }

    /**
     * @param int $periodIndex
     * @return array
     */
    public function statistics(int $periodIndex): array
    {
        $now = CarbonImmutable::now();
        $keysPeriod = [];
        $statistics = [];

        $startPeriod = match ($periodIndex) {
            1 => $now->addDays(-6)->startOfDay(),
            2 => $now->addDays(-13)->startOfDay(),
            3 => $now->addMonthsNoOverflow(-1)->startOfDay(),
            //here with a margin, because the control points are rounded down, and the values may be missing.
            4 => $now->addMonthsNoOverflow(-6)->addDays(-16)->startOfDay(),
            5 => $now->addYears(-1)->startOfDay(),
        };

        $startRange = $now->diffInDays($startPeriod);
        foreach (range(-1 * $startRange, 0) as $number) {
            $keysPeriod[$now->addDays($number)->toDateString()] = 0;
        }

        //colors
        $colorsArr = Cache::get('colors');
        $physicalExercises = User::with('physicalExercises')->where('id', Auth::id())->first()->physicalExercises()->pluck('name')->toArray();

        if (!$colorsArr || is_array($colorsArr) && !empty(array_diff($physicalExercises, array_keys($colorsArr)))) {
            $colorsArr = [];
            if ($physicalExercises) {
                foreach ($physicalExercises as $physicalExerciseId) {
                    $colorsArr[$physicalExerciseId] = StringHelper::randomColor();
                }
            }
            Cache::put('colors', $colorsArr, $now->addWeek());
        }

        DB::statement("SET SQL_MODE=''");
        $subSelectExercisesWithCount = UserPhysicalExercise::select('physical_exercise_id', DB::raw('count(id) as exercises_count'))
            ->where('user_physical_exercises.created_at', '>=', $startPeriod)
            ->where('user_physical_exercises.created_at', '<=', $now)
            ->groupBy('physical_exercise_id')
            ->orderBy('exercises_count', 'desc')
            ->take(10);

        $userPhysicalExercises = UserPhysicalExercise::select(
            'user_physical_exercises.physical_exercise_id',
            DB::raw('SUM(count) AS sum_count'),
            DB::raw('DATE_FORMAT(user_physical_exercises.created_at, "%Y-%m-%d") AS rounded_date'),
            'physical_exercises.name as name'
        )
            ->leftJoin('physical_exercises', 'physical_exercises.id', '=', 'user_physical_exercises.physical_exercise_id')
            ->joinSub($subSelectExercisesWithCount, 'exercises_with_count', function ($join) {
                $join->on('user_physical_exercises.physical_exercise_id', '=', 'exercises_with_count.physical_exercise_id');
            })
            ->where('user_physical_exercises.created_at', '>=', $startPeriod)
            ->where('user_physical_exercises.created_at', '<=', $now)
            ->groupBy('physical_exercise_id')
            ->groupBy('rounded_date')
            ->groupBy('name')
            ->orderBy('rounded_date')
            ->get()
            ->toArray();

        array_walk($userPhysicalExercises, function ($item, $key) use (&$statistics) {
            $statistics[$item['name']][$item['rounded_date']] = $item['sum_count'];
        });

        foreach ($statistics as &$statisticsItem) {
            $statisticsItem = array_merge($keysPeriod, $statisticsItem);
        }

        unset($statisticsItem);

        if ($periodIndex == 4) {
            $statisticsAux = [];

            $monthBegin = $now->firstOfMonth();
            //measure the middle of the month
            $monthMiddle = $now->firstOfMonth()->addDays(14)->endOfDay();

            $keysPeriodHalfYear = [];
            for ($i = 0; $i <= 6; $i++) {
                $dayKeyAux = $now->firstOfMonth();
                if ($now->between($monthBegin, $monthMiddle)) {
                    $keysPeriodHalfYear[$dayKeyAux->addMonthsNoOverflow(-1 * $i)->firstOfMonth()->toDateString()] = 0;
                    if ($i != 6) $keysPeriodHalfYear[$dayKeyAux->addMonthsNoOverflow(-1 * $i - 1)->addDays(14)->toDateString()] = 0;
                } else {
                    $keysPeriodHalfYear[$dayKeyAux->addMonthsNoOverflow(-1 * $i)->addDays(14)->toDateString()] = 0;
                    if ($i != 6) $keysPeriodHalfYear[$dayKeyAux->addMonthsNoOverflow(-1 * $i)->firstOfMonth()->toDateString()] = 0;
                }
            }
            $keysPeriodHalfYear = array_reverse($keysPeriodHalfYear);

            foreach ($statistics as $statisticsKey => $statisticsItem) {
                foreach ($keysPeriodHalfYear as $dayKeyStr => $value) {
                    $dayKey = CarbonImmutable::parse($dayKeyStr);
                    $this->statisticsLoopAux($dayKey, $dayKeyStr, $statisticsKey, $statisticsItem, $statisticsAux);
                }
            }

            $keysPeriod = $keysPeriodHalfYear;
            $statistics = $statisticsAux;
        }

        if ($periodIndex == 5) {
            $statisticsAux = [];
            $keysPeriodYear = [];
            foreach ($statistics as $statisticsKey => $statisticsItem) {
                for ($i = 0; $i <= 12; $i++) {
                    $dayKey = $now->addMonthsNoOverflow(-1 * $i);
                    $dayKeyStr = $dayKey->toDateString();

                    if (!array_key_exists($dayKeyStr, $keysPeriodYear)) {
                        $keysPeriodYear[$dayKeyStr] = 0;
                    }
                    $this->statisticsLoopAux($dayKey, $dayKeyStr, $statisticsKey, $statisticsItem, $statisticsAux);
                }
            }
            $statistics = $statisticsAux;
            $keysPeriod = array_reverse($keysPeriodYear);
        }

        return [
            'keysPeriod' => $keysPeriod,
            'statistics' => $statistics,
            'colors' => $colorsArr
        ];
    }


    /**
     * @param CarbonImmutable $dayKey
     * @param string $dayKeyStr
     * @param string $statisticsKey
     * @param array $statisticsItem
     * @param array $statisticsAux
     */
    private function statisticsLoopAux(CarbonImmutable $dayKey, string $dayKeyStr, string $statisticsKey, array $statisticsItem, array &$statisticsAux)
    {
        if (!empty($statisticsItem[$dayKeyStr])) {
            $statisticsAux[$statisticsKey][$dayKeyStr] = $statisticsItem[$dayKeyStr];
        } else {
            $nearValueSuccessFlag = false;
            for ($k = 1; $k <= 5; $k++) {
                $dayKeyAux = $dayKey->addDays($k)->toDateString();
                if (!empty($statisticsItem[$dayKeyAux])) {
                    $statisticsAux[$statisticsKey][$dayKeyStr] = $statisticsItem[$dayKeyAux];
                    $nearValueSuccessFlag = true;
                    break;
                }
                $dayKeyAux = $dayKey->addDays(-1 * $k)->toDateString();
                if (!empty($statisticsItem[$dayKeyAux])) {
                    $statisticsAux[$statisticsKey][$dayKeyStr] = $statisticsItem[$dayKeyAux];
                    $nearValueSuccessFlag = true;
                    break;
                }
            }
            if (!$nearValueSuccessFlag) $statisticsAux[$statisticsKey][$dayKeyStr] = $statisticsItem[$dayKeyStr];
        }
    }

}
