<?php

namespace App\Services;

use App\Models\UserPhysicalExercise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarService
{

    /**
     * @return array
     */
    public function getCalendar($date): array
    {
        $now = Carbon::now();
        $currentMonth = $date->month;
        $day = $now->month == $date->month && $now->year == $date->year ? $now->day : 0;
        $weeksInMonth = $this->crossedWeeksCount($date);
        $beginDate = $date->firstOfMonth()->startOfWeek();
        $resultAux = [];
        for ($i = 0; $i <= ($weeksInMonth) * 7 - 1; $i++) {
            $beginDateAux = $beginDate->clone();
            array_push($resultAux, [
                'date' => $beginDateAux->addDays($i)->day,
                'full_date' => $beginDate->clone()->addDays($i)->toDateString(),
                'is_current_date' => $currentMonth === $beginDateAux->month && $day === $beginDateAux->day,
                'month' => $beginDateAux->format('m'),
                'is_this_month' => $currentMonth === $beginDateAux->month
            ]);
        }

        $exercises = UserPhysicalExercise::select(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as rounded_date'), \DB::raw('count(*) as count'))
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $resultAux[0]['full_date'])
            ->where('created_at', '<=', $resultAux[count($resultAux) - 1]['full_date'])
            ->groupBy('rounded_date')
            ->orderBy('rounded_date')
            ->pluck('count', 'rounded_date')
            ->toArray();

        $result = array_chunk($resultAux, 7);

        foreach ($result as $keyWeek => &$subArr) {
            foreach ($subArr as $keyDay => &$item) {
                if ($keyWeek === 0) {
                    $item['week'] = 'first_week';
                }
                if ($keyWeek === count($result) - 1) {
                    $item['week'] = 'last_week';
                }
                if ($keyDay === 0) {
                    $item['day'] = 'first_day';
                }
                if ($keyDay === 6) {
                    $item['day'] = 'last_day';
                }
                if (!empty($exercises[$item['full_date']])) $item['exercises_count'] = $exercises[$item['full_date']];
            }
        }
        return $result;
    }


    /**
     * @return int
     */
    private function crossedWeeksCount($date): int
    {
        $firstWeekOfMonthDate = $date->clone()->startOfMonth()->startOfWeek();
        $lastWeekOfMonthDate = $date->clone()->endOfMonth()->endOfWeek();
        return ceil($lastWeekOfMonthDate->diffInDays($firstWeekOfMonthDate) / 7);
    }
}
