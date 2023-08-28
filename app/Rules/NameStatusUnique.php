<?php

namespace App\Rules;

use App\Models\PhysicalExercise;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NameStatusUnique implements Rule
{
    private Request $request;

    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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

        if ($requestData['status'] == PhysicalExercise::STATUS_PRIVATE) {
            $this->message = 'Упражнение с таким именем, доступное только для вас, уже существует';
            return empty(PhysicalExercise::where('private_name', $value)->where('created_by', Auth::id())->first());
        } elseif ($requestData['status'] == PhysicalExercise::STATUS_PUBLIC) {
            $this->message = 'Упражнение с таким именем, доступное для других пользователей, уже существует';
            return empty(PhysicalExercise::where('name', $value)->first());
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
