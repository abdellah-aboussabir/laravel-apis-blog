<?php

namespace App\Http\Requests\Post;

use App\Http\Traits\GeneraleTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
{
    use GeneraleTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "user_id" => 'required|integer',
            "content" => 'required|string|min:20',
            "title" => 'required|string|min:8',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnError(422, "the given data invalid", $validator->errors()));
    }
}
