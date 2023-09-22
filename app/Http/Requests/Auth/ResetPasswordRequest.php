<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\GeneraleTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{

    use GeneraleTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "email" => "required|email",
            'password' => [
                'required',
                'string',
            ],
            'password_confirmation' => [
                'required_with:password',
                'same:password',
            ],
            'updated_at'        => ['required', 'date', 'date_format:Y-m-d H:i:s'],

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    public function failedValidation(Validator  $validator)
    {
        throw new HttpResponseException($this->returnError(422, "The given data was invalid.", $validator->errors()));
    }
}
