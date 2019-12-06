<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterTimeRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'date'       => 'sometimes|date|date_equals:today',
            'start_time' => 'sometimes|date|date_equals:today',
            'end_time'   => 'sometimes|date|date_equals:today'
        ];
    }
}
