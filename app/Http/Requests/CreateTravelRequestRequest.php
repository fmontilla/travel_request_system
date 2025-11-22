<?php

namespace App\Http\Requests;

class CreateTravelRequestRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requester_name' => ['required', 'string', 'min:2', 'max:255'],
            'destination' => ['required', 'string', 'min:2', 'max:255'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after:departure_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'requester_name.required' => 'Requester name is required.',
            'requester_name.min' => 'Requester name must be at least 2 characters.',
            'requester_name.max' => 'Requester name cannot exceed 255 characters.',
            'destination.required' => 'Destination is required.',
            'destination.min' => 'Destination must be at least 2 characters.',
            'destination.max' => 'Destination cannot exceed 255 characters.',
            'departure_date.required' => 'Departure date is required.',
            'departure_date.date' => 'Departure date must be a valid date.',
            'departure_date.after_or_equal' => 'The departure date must be today or after.',
            'return_date.required' => 'Return date is required.',
            'return_date.date' => 'Return date must be a valid date.',
            'return_date.after' => 'The return date must be after departure date.',
        ];
    }
}
