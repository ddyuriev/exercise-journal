<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhysicalExercises\StorePhysicalExercisesRequest;
use App\Http\Requests\PhysicalExercises\UpdatePhysicalExercisesRequest;
use App\Models\PhysicalExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhysicalExerciseController extends Controller
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $perPage;

    /**
     * SettingsController constructor.
     */
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
        $data = $request->all();
        return view('physical_exercise.index', [
            'physical_exercises' => $this->searchQuery($data),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('physical_exercise.create');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $physicalExercise = PhysicalExercise::find($id);
        if (!Auth::user()->is_admin) {
            $physicalExercise->name = $physicalExercise->private_name;
        }

        return view('physical_exercise.edit', [
            'physical_exercise' => $physicalExercise
        ]);
    }

    public function update(UpdatePhysicalExercisesRequest $request, $id)
    {
        $requestData = $request->all();

        $physicalExercise = PhysicalExercise::find($id);
        $physicalExercise->name = $requestData['status'] == PhysicalExercise::STATUS_PUBLIC ? $requestData['name'] : $requestData['name'] . '_' . substr(hash('sha256', uniqid(mt_rand(), true)), 0, 8);
        $physicalExercise->private_name = $requestData['name'];
        $physicalExercise->status = $requestData['status'];
        $physicalExercise->description = $requestData['description'];
        $physicalExercise->save();

        return redirect()->route('settings.physical-exercises.index')->with(['alert-type' => 'success', 'message' => __('Physical Exercise Updated')]);
    }


    public function store(StorePhysicalExercisesRequest $request)
    {
        $requestData = $request->all();

        /**
         *  `name` field must be unique.
         *  `name` or `private_name` must be present
         */
        $createData = [
            'name' => $requestData['status'] == PhysicalExercise::STATUS_PUBLIC ? $requestData['name'] : $requestData['name'] . '_' . substr(hash('sha256', uniqid(mt_rand(), true)), 0, 8),
            'private_name' => $requestData['name'],
            'status' => $requestData['status'],
            'description' => $requestData['description'],
            'created_by' => Auth::id(),
        ];

        $physicalExercise = PhysicalExercise::create($createData);
        Auth::user()->physicalExercises()->attach([$physicalExercise->id]);

        return redirect()->route('settings.physical-exercises.index')->with(['alert-type' => 'success', 'message' => __('Physical Exercise Created')]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toggle(Request $request)
    {
        $data = $request->all();
        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();
        array_key_exists($data['physicalExerciseId'], $userPhysicalExercises) ? Auth::user()->physicalExercises()->detach([$data['physicalExerciseId']]) : Auth::user()->physicalExercises()->attach([$data['physicalExerciseId']]);

        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();

        $page = 1;
        $output = [];
        if (!empty($data['queryString']) && !empty($queryStringParsed = parse_url($data['queryString']))) {
            if (!empty($queryStringParsed['query'])) {
                parse_str($queryStringParsed['query'], $output);
                if (!empty($output['page'])) {
                    $page = $output['page'];
                }
            }
        }
        $physicalExercises = $this->searchQuery($output, $page);
        return [
            'is_success' => true,
            'items' => [
                'toggle_position' => array_key_exists($data['physicalExerciseId'], $userPhysicalExercises),
                'physical_exercises' => $physicalExercises,
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $data = $request->all();

        $physicalExercises = $this->searchQuery($data, 1);
        $paginationLinks = (string)$physicalExercises->withQueryString()->onEachSide(1)->links();
        $paginationLinks = str_replace("/search", "", $paginationLinks);


        if ($paginationLinks) {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($paginationLinks);
            $xpath = new \DOMXPath($dom);
            $elements = $xpath->query('//div');
            foreach ($elements as $el) {
                $el->parentNode->removeChild($el);
            }
            $pagination = $dom->saveHTML();
        } else {
            $pagination = '';
        }
        return [
            'is_success' => true,
            'items' => [
                'physical_exercises' => $physicalExercises,
            ],
            'pagination' => $pagination
        ];
    }

    /**
     * @param array $searchData
     * @param int|null $pageNumber
     * @return mixed
     */
    private function searchQuery(array $searchData, int $pageNumber = null)
    {
        $user = Auth::user();

        $physicalExercisesQuery = PhysicalExercise::select(DB::raw(
            'id, name, private_name, status, created_by, sub_sel.user_id as user_id, sub_sel.updated_at'
        ))
            ->leftJoin(DB::raw(
                <<<STR
(SELECT *
    FROM `physical_exercise_user` pe_u
    WHERE pe_u.user_id = $user->id ) AS sub_sel
STR
            ),
                function ($join) {
                    $join->on('id', '=', 'sub_sel.physical_exercise_id');
                })
            ->orderBy('sub_sel.user_id', 'DESC')
            ->orderBy('sub_sel.updated_at')
            ->orderBy('id');

        /**
         * hide non confirmed physicalExercises of other users
         */
        if (!$user->is_admin) {
            $physicalExercisesQuery->where(function ($subQuery) use ($user) {
                $subQuery->where('status', PhysicalExercise::STATUS_CONFIRMED);
                $subQuery->orWhere(function ($subSubQuery) use ($user) {
                    $subSubQuery->whereIn('status', [PhysicalExercise::STATUS_PRIVATE, PhysicalExercise::STATUS_PUBLIC])
                        ->where('created_by', $user->id);
                });
            });
        }

        if (!empty($searchData['name'])) {
            $physicalExercisesQuery->where('name', 'like', "%{$searchData['name']}%");
        }

        if ($pageNumber) {
            $result = $physicalExercisesQuery->paginate($this->perPage, ['*'], 'page', $pageNumber);
        } else {
            $result = $physicalExercisesQuery->paginate($this->perPage);
        }
        return $result;
    }
}
