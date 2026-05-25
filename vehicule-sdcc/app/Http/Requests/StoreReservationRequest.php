<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'car_id' => 'required|integer|exists:cars,id',
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'kilometers' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:2000',
            'status' => 'nullable|in:pending,approved',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'L\'utilisateur est requis.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'car_id.required' => 'Le véhicule est requis.',
            'car_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'destination.required' => 'La destination est requise.',
            'start_date.required' => 'La date de départ est requise.',
            'end_date.required' => 'La date de retour est requise.',
            'end_date.after_or_equal' => 'La date de retour doit être >= à la date de départ.',
            'reason.required' => 'La raison de la réservation est requise.',
        ];
    }
}
