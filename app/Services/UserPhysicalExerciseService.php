<?php

namespace App\Services;

use App\Models\PhysicalExercise;
use App\Models\UserPhysicalExercise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPhysicalExerciseService
{

    public function getUserPhysicalExercises(Carbon $date, ?int $page): LengthAwarePaginator
    {
        $perPage = config('pagination.settings.per_page');

        $statusApproved = PhysicalExercise::STATUS_APPROVED;
        return UserPhysicalExercise
            ::with(['physical_exercises' => function ($query) use ($statusApproved) {
                $query->select(
                    DB::raw(
                        "id,
                    case
                        when status = $statusApproved then name
                        else private_name
                    end as name,
                    description, status, created_by, created_at, updated_at"
                    )
                );
            }])
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $date->startOfDay())
            ->where('created_at', '<=', $date->clone()->endOfDay())
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
