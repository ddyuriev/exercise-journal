<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;


class StatisticsRequest extends FormRequest
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
            'period' => ['in:1,2,3,4,5']
        ];
    }
}
