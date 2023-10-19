<?php

namespace App\Http\Requests\PhysicalExercises;

use App\Rules\PhysicalExercise\NameStatusUnique;
use Illuminate\Foundation\Http\FormRequest;


class StorePhysicalExerciseRequest extends FormRequest
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
            'name' => ['required', new NameStatusUnique(request())],
            'status' => ['required', 'in:1,2'],
        ];
    }

}

