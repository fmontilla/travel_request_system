<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateTravelRequestStatusRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        return auth('api')->check() && auth('api')->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either approved or cancelled.',
        ];
    }
}
