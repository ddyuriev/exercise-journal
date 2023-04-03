<?php

namespace App\Services;

use Carbon\Carbon;

class CalendarService
{

    /**
     * @return array
     */
    public function getCalendar(): array
    {
        $now = Carbon::now()->locale('ru_RU');
        $currentMonth = $now->month;
        $day = $now->day;
        $weeksInMonth = $this->crossedWeeksCount();

        $date = Carbon::now()->firstOfMonth()->startOfWeek();
        $resultAux = [];
        for ($i = 0; $i <= ($weeksInMonth) * 7 - 1; $i++) {
            $dateAux = $date->clone();
            array_push($resultAux, [
                'date' => $dateAux->addDays($i)->day,
                'is_current_date' => $currentMonth === $dateAux->month && $day === $dateAux->day,
                'month' => $dateAux->format('m'),
                'is_this_month' => $currentMonth === $dateAux->month
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
    private function crossedWeeksCount(): int
    {
        $firstWeekOfMonthDate = Carbon::now()->startOfMonth()->startOfWeek();
        $lastWeekOfMonthDate = Carbon::now()->endOfMonth()->endOfWeek();
        return ceil($lastWeekOfMonthDate->diffInDays($firstWeekOfMonthDate) / 7);
    }
}
