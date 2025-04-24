<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['min:1' , 'max:100' , 'required'],
            'nomor_identitas' => ['min:10' , 'max:100' , 'required'],
            'email' => ['min:10' , 'max:100' , 'required'],
            'role' => ['min:1' , 'max:100'],
            'password' => ['min:5' , 'max:100' , 'required'],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(response()->json([
            'message' => 'validate fail',
            'errors' => $validator->errors(),
        ] , 401));
    }
}
