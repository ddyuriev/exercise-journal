<?php

namespace App\Rules\PhysicalExercise;

use App\Models\PhysicalExercise;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NameStatusUnique implements Rule
{
    private Request $request;

    private $message;

    private $id;


    /**
     * NameStatusUnique constructor.
     * @param Request $request
     * @param string|null $id
     */
    public function __construct(Request $request, string|null $id = null)
    {
        $this->request = $request;
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $requestData = $this->request->all();

        $physicalExerciseQuery = PhysicalExercise::query();
        if ($this->id) {
            $physicalExerciseQuery->whereNot('id', $this->id);
        }

        if ($requestData['status'] == PhysicalExercise::STATUS_PRIVATE) {
            $this->message = 'Упражнение с таким именем, доступное только для вас, уже существует';
            return empty($physicalExerciseQuery->where('private_name', $value)->where('created_by', Auth::id())->first());
        } elseif ($requestData['status'] == PhysicalExercise::STATUS_IN_MODERATION) {
            $this->message = 'Упражнение с таким именем, доступное для других пользователей, уже существует';
            return empty($physicalExerciseQuery->where('name', $value)->first());
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
