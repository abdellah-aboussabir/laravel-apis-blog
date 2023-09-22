<?php

namespace App\Http\Requests\Auth;

use App\Http\Traits\GeneraleTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{

    use GeneraleTrait;
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
            "name" => "required|string",
            "email" => 'required|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            "password" => "required|string",

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

    public function failedValidation(Validator $validator)
    {
        if ($validator->errors()->has('email')) {
            $errorMessage = $validator->errors()->first('email');

            // Check if error message indicates email is not unique
            if (strpos($errorMessage, 'has already been taken') !== false) {
                throw new HttpResponseException($this->returnError(409, "The given email address is already in use"));
            }
        }

        throw new HttpResponseException($this->returnError(422, "The given data was invalid."));
    }
}
