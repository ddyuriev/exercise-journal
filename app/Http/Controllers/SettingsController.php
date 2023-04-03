<?php

namespace App\Http\Controllers;

use App\Models\PhysicalExercise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    private $perPage;

    public function __construct()
    {
        $this->perPage = config('pagination.settings.per_page');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return view('settings.index');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function physicalExercisesIndex(Request $request)
    {
        $physicalExercises = PhysicalExercise
            ::withCount(['users' => function ($query) {
                $query->where('users.id', Auth::id());
            }])
            ->orderByDesc('users_count')
            ->orderBy('updated_at')
            ->paginate($this->perPage);

        return view('settings.physicalExercisesIndex', [
            'physical_exercises' => $physicalExercises,
        ]);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function physicalExercisesToggle(Request $request)
    {
        $data = $request->all();

        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();
        array_key_exists($data['physicalExerciseId'], $userPhysicalExercises) ? Auth::user()->physicalExercises()->detach([$data['physicalExerciseId']]) : Auth::user()->physicalExercises()->attach([$data['physicalExerciseId']]);

        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();

        $physicalExercisesQuery = PhysicalExercise
            ::withCount(['users' => function ($query) {
                $query->where('users.id', Auth::id());
            }])
            ->orderByDesc('users_count')
            ->orderBy('updated_at');

        $page = 1;
        if (!empty($data['queryString']) && !empty($queryStringParsed = parse_url($data['queryString']))) {
            if (!empty($queryStringParsed['query'])) {
                parse_str($queryStringParsed['query'], $output);
                if (!empty($output['page'])) {
                    $page = $output['page'];
                }
            }
        }
        $physicalExercises = $physicalExercisesQuery->paginate($this->perPage, ['*'], 'page', $page);

        return [
            'is_success' => true,
            'items' => [
                'toggle_position' => array_key_exists($data['physicalExerciseId'], $userPhysicalExercises),
                'physical_exercises' => $physicalExercises,
            ]
        ];
    }
}
