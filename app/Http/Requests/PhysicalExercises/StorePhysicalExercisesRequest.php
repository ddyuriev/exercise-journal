<?php

namespace App\Http\Requests\PhysicalExercises;

use Illuminate\Foundation\Http\FormRequest;


class StorePhysicalExercisesRequest extends FormRequest
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
            'name' => ['required', 'unique:physical_exercises,name'],
        ];
    }

    public function messages(){

        return [
            'name.unique' => "Такое упражнение уже существует",
        ];
    }


}

