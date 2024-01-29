<?php

use App\Models\PhysicalExercise;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

if (!function_exists('physicalExerciseIntToName')) {
    function physicalExerciseIntToName(int $status)
    {
        return match ($status) {
            PhysicalExercise::STATUS_PRIVATE => 'Приватное',
            PhysicalExercise::STATUS_IN_MODERATION => 'Общее, в модерации',
            PhysicalExercise::STATUS_APPROVED => 'Общее, подтверждено',
            PhysicalExercise::STATUS_REJECTED => 'Приватное, общий статус отклонен',
        };
    }
}

if (!function_exists('statusesDifferentFromApproved')) {
    function statusesDifferentFromApproved()
    {
        return [
            PhysicalExercise::STATUS_PRIVATE,
            PhysicalExercise::STATUS_IN_MODERATION,
            PhysicalExercise::STATUS_REJECTED
        ];
    }
}

if (!function_exists('editablePhysicalExercises')) {
    function editablePhysicalExercises()
    {
        return [
            PhysicalExercise::STATUS_PRIVATE,
            PhysicalExercise::STATUS_IN_MODERATION,
        ];
    }
}

if (!function_exists('getMonthNameLoc')) {
    function getMonthNameLoc($number)
    {
        /**
         * @psalm-suppress InvalidPropertyFetch
         */
        return mb_convert_case(mb_substr(Carbon::now()->month($number)->locale(App::getLocale())->monthName, 0, 3), MB_CASE_TITLE, "UTF-8");
    }
}
