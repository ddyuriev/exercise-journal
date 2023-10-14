<?php

use App\Models\PhysicalExercise;

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
