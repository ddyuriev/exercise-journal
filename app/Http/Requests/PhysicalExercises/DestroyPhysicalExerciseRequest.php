<?php

namespace App\Http\Requests\PhysicalExercises;

use Illuminate\Foundation\Http\FormRequest;

class DestroyPhysicalExerciseRequest extends FormRequest
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
     * @return \string[][]
     */
    public function rules()
    {
        return [
            'queryString' => ['string'],
        ];
    }
}

