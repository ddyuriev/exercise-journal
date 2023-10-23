<?php

namespace App\Http\Requests\PhysicalExercises;

use Illuminate\Foundation\Http\FormRequest;


class TogglePhysicalExerciseRequest extends FormRequest
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
        return [
            'physicalExerciseId' => ['exists:App\Models\PhysicalExercise,id'],
        ];
    }

}

