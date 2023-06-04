<?php

namespace App\Services;

use Carbon\Carbon;

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
                'is_current_date' => $currentMonth === $beginDateAux->month && $day === $beginDateAux->day,
                'month' => $beginDateAux->format('m'),
                'is_this_month' => $currentMonth === $beginDateAux->month
            ]);
        }

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
