<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'Unable to complete registration. Please try again or contact support.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    protected function getValidationErrorMessage(Validator $validator): string
    {
        $errors = $validator->errors();

        if ($errors->has('email') && str_contains($errors->first('email'), 'already been taken')) {
            return 'Unable to complete registration. Please verify your information.';
        }

        return parent::getValidationErrorMessage($validator);
    }
}
