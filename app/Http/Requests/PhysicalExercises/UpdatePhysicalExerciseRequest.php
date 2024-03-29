<?php

namespace App\Http\Requests\PhysicalExercises;

use App\Rules\PhysicalExercise\NameStatusUnique;
use Illuminate\Foundation\Http\FormRequest;


class UpdatePhysicalExerciseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $editablePhysicalExercises = implode(',', editablePhysicalExercises());
        return [
            'name' => ['required', new NameStatusUnique(request(), $this->route('physical_exercise'))],
            'status' => ['required', "in:$editablePhysicalExercises"],
        ];
    }
}

