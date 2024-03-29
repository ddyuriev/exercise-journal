<?php

namespace App\Http\Requests\UserPhysicalExercises;

use Illuminate\Foundation\Http\FormRequest;


class UpdateUserPhysicalExerciseRequest extends FormRequest
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
            'count' => ['integer', 'min:0'],
            'comment' => ['string', 'nullable']
        ];
    }
}
