<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\PhysicalExercises\DestroyPhysicalExerciseRequest;
use App\Http\Requests\PhysicalExercises\StorePhysicalExerciseRequest;
use App\Http\Requests\PhysicalExercises\TogglePhysicalExerciseRequest;
use App\Http\Requests\PhysicalExercises\UpdatePhysicalExerciseRequest;
use App\Models\PhysicalExercise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PhysicalExerciseController extends Controller
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $perPage;


    public function __construct()
    {
        $this->perPage = config('pagination.settings.per_page');
    }

    public function index(Request $request): View
    {
        $data = $request->all();

        return view('physical_exercise.index', [
            'physical_exercises' => $this->searchQuery($data),
        ]);
    }

    public function create(): View
    {
        return view('physical_exercise.create');
    }

    public function edit(int $id)
    {
        $physicalExercise = PhysicalExercise::find($id);

        if ($physicalExercise->status == PhysicalExercise::STATUS_PRIVATE) {
            $physicalExercise->name = $physicalExercise->private_name;
        }

        return view('physical_exercise.edit', [
            'physical_exercise' => $physicalExercise
        ]);
    }

    public function show(int $id): View
    {
        $physicalExercise = PhysicalExercise::find($id);

        //can't view other users records, differents from STATUS_APPROVED
        if (!$physicalExercise || in_array($physicalExercise->status, [PhysicalExercise::STATUS_PRIVATE, PhysicalExercise::STATUS_IN_MODERATION, PhysicalExercise::STATUS_REJECTED]) && Auth::id() != $physicalExercise->created_by) {
            abort(404);
        }
        if ($physicalExercise->status == PhysicalExercise::STATUS_PRIVATE || $physicalExercise->status == PhysicalExercise::STATUS_REJECTED) {
            $physicalExercise->name = $physicalExercise->private_name;
        }

        return view('physical_exercise.show', [
            'physical_exercise' => $physicalExercise
        ]);
    }

    public function update(UpdatePhysicalExerciseRequest $request, int $id): JsonResponse
    {
        $requestData = $request->all();

        $physicalExercise = PhysicalExercise::find($id);
        $physicalExercise->name = $requestData['status'] == PhysicalExercise::STATUS_IN_MODERATION ? $requestData['name'] : $requestData['name'] . '_' . substr(hash('sha256', uniqid(mt_rand(), true)), 0, 8);
        $physicalExercise->private_name = $requestData['name'];
        $physicalExercise->status = $requestData['status'];
        $physicalExercise->description = $requestData['description'];

        try {
            $physicalExercise->save();
        } catch (\Throwable $exception) {
            return response()->json([
                'is_success' => false,
                'errors' => [$exception->getMessage()]
            ]);
        }

        return response()->json([
            'is_success' => true,
        ]);
    }

    public function store(StorePhysicalExerciseRequest $request): JsonResponse
    {
        $requestData = $request->all();

        /**
         *  `name` field must be unique.
         *  `name` or `private_name` must be present
         */
        $createData = [
            'name' => $requestData['status'] == PhysicalExercise::STATUS_IN_MODERATION ? $requestData['name'] : $requestData['name'] . '_' . substr(hash('sha256', uniqid(mt_rand(), true)), 0, 8),
            'private_name' => $requestData['name'],
            'status' => $requestData['status'],
            'description' => $requestData['description'],
            'created_by' => Auth::id(),
        ];

        try {
            $physicalExercise = PhysicalExercise::create($createData);
            Auth::user()->physicalExercises()->attach([$physicalExercise->id]);

        } catch (\Throwable $exception) {
            return response()->json([
                'is_success' => false,
                'errors' => [$exception->getMessage()]
            ]);
        }

        return response()->json([
            'is_success' => true,
        ]);
    }

    public function destroy(DestroyPhysicalExerciseRequest $request, int $id): JsonResponse
    {
        $data = $request->all();

        $physicalExercise = PhysicalExercise::where('created_by', Auth::id())
            ->where('id', $id)
            ->whereIn('status', statusesDifferentFromApproved())
            ->first();

        if (empty($physicalExercise)) {
            return response()->json([
                'is_success' => false
            ]);
        }

        $queryStringParsedArray = StringHelper::httpQueryStringParser($data['queryString']);

        $itemsOld = $this->searchQuery($queryStringParsedArray, $queryStringParsedArray['page']);

        try {
            $physicalExercise->delete();
        } catch (\Throwable $exception) {
            Log::channel('db_errors')->error('PhysicalExercise delete error', ['user' => Auth::user(), 'model' => $physicalExercise, 'exception' => $exception->getMessage()]);
            return response()->json([
                'is_success' => false,
                'errors' => ['internal error']
            ]);
        }

        $physicalExercises = $this->searchQuery($queryStringParsedArray, $queryStringParsedArray['page']);
        $isNeedReload = $itemsOld->total() !== 0 && ceil($itemsOld->total() / $this->perPage) !== ceil(($itemsOld->total() - 1) / $this->perPage);

        $newQueryString = '';
        if ($isNeedReload && $queryStringParsedArray['page'] > 1) {
            $queryStringParsedArray['page'] -=  1;
            $newQueryString = http_build_query($queryStringParsedArray);
        }

        return response()->json([
            'is_success' => true,
            'is_need_reload' => $isNeedReload,
            'new_query_string' => "?$newQueryString",
            'items' => $physicalExercises,
        ]);
    }

    public function toggle(TogglePhysicalExerciseRequest $request): JsonResponse
    {
        $data = $request->all();
        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();
        array_key_exists($data['physicalExerciseId'], $userPhysicalExercises) ? Auth::user()->physicalExercises()->detach([$data['physicalExerciseId']]) : Auth::user()->physicalExercises()->attach([$data['physicalExerciseId']]);

        $userPhysicalExercises = Auth::user()->physicalExercises()
            ->pluck('user_id', 'physical_exercise_id')
            ->toArray();

        $queryStringParsedArr = StringHelper::httpQueryStringParser($data['queryString']);
        $physicalExercises = $this->searchQuery($queryStringParsedArr, $queryStringParsedArr['page']);

        return response()->json([
            'is_success' => true,
            'items' => [
                'toggle_position' => array_key_exists($data['physicalExerciseId'], $userPhysicalExercises),
                'physical_exercises' => $physicalExercises,
            ]
        ]);
    }

    public function search(string $searchString): JsonResponse
    {
        $searchString = str_replace('&', '%26', $searchString);
        parse_str($searchString, $data);

        $physicalExercises = $this->searchQuery($data, 1);

        $physicalExercises->setPath(route('settings.physical-exercises.index'));
        $paginationLinks = (string)$physicalExercises->appends('search_query', $data['search_query'])->onEachSide(1)->links();

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
        return response()->json([
            'is_success' => true,
            'items' => [
                'physical_exercises' => $physicalExercises,
            ],
            'search' => $data['search_query'],
            'pagination' => $pagination
        ]);
    }


    private function searchQuery(array $searchData, int $pageNumber = null): LengthAwarePaginator
    {
        $user = Auth::user();

        $statusApproved = PhysicalExercise::STATUS_APPROVED;

        $physicalExercisesQuery = PhysicalExercise::select(DB::raw(
            "id,
                    case
                        when status = $statusApproved then name
                        else private_name
                    end as name,
               description, status, created_by, sub_sel.user_id as user_id, sub_sel.updated_at"
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
        $physicalExercisesQuery->where(function ($subQuery) use ($user) {
            $subQuery->where('status', PhysicalExercise::STATUS_APPROVED);
            $subQuery->orWhere(function ($subSubQuery) use ($user) {
                $subSubQuery->whereIn('status', [PhysicalExercise::STATUS_PRIVATE, PhysicalExercise::STATUS_IN_MODERATION, PhysicalExercise::STATUS_REJECTED])
                    ->where('created_by', $user->id);
            });
        });

        if (!empty($searchData['search_query'])) {
            $physicalExercisesQuery->where(function (Builder $query) use ($searchData) {
                $query->where('name', 'like', "{$searchData['search_query']}%")
                    ->orWhere('private_name', 'like', "{$searchData['search_query']}%");
            });
        }

        if ($pageNumber) {
            $result = $physicalExercisesQuery->paginate($this->perPage, ['*'], 'page', $pageNumber);
        } else {
            $result = $physicalExercisesQuery->paginate($this->perPage);
        }
        return $result;
    }
}
