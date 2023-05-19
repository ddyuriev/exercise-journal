<?php

namespace App\Http\Controllers;

use App\Models\PhysicalExercise;
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
        $data = $request->all();
        return view('settings.physicalExercisesIndex', [
            'physical_exercises' => $this->searchQuery($data),
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
    public function searchQuery(array $searchData, int $pageNumber = null)
    {
        $userId = Auth::id();

        $physicalExercises = PhysicalExercise::select(\DB::raw(
            'id, name, sub_sel.user_id as active, sub_sel.updated_at'
        ))
            ->leftJoin(\DB::raw(
                <<<STR
            (SELECT *
            FROM `physical_exercise_user` pe_u
            WHERE pe_u.user_id = {$userId} ) AS sub_sel
STR
            ),
                function ($join) {
                    $join->on('id', '=', 'sub_sel.physical_exercise_id');
                })
            ->orderBy('sub_sel.user_id', 'DESC')
            ->orderBy('sub_sel.updated_at')
            ->orderBy('id');

        if (!empty($searchData['name'])) {
            $physicalExercises->where('name', 'like', "%{$searchData['name']}%");
        }

        if ($pageNumber) {
            $result = $physicalExercises->paginate($this->perPage, ['*'], 'page', $pageNumber);
        } else {
            $result = $physicalExercises->paginate($this->perPage);
        }
        return $result;
    }
}
