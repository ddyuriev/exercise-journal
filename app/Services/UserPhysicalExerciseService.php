<?php

namespace App\Services;

use App\Models\UserPhysicalExercise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserPhysicalExerciseService
{

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserPhysicalExercises($date, $page)
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

}
