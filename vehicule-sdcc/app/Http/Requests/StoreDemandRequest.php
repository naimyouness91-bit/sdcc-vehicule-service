<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDemandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Utilisateur peut créer uniquement pour lui-même
        // Les admins peuvent créer pour d'autres utilisateurs
        return auth()->check() && (
            auth()->user()->hasRole(['admin', 'super_admin']) ||
            (int) $this->input('user_id') === auth()->id()
        );
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
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'kilometers' => 'nullable|integer|min:0',
            'distance_travelled' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:1000',
            // Seuls les admins peuvent définir le statut
            'status' => auth()->user()?->hasRole(['admin', 'super_admin']) 
                ? Rule::in(['pending', 'approved', 'rejected', 'cancelled'])
                : 'prohibited',
        ];
    }

    /**
     * Get custom validation messages.
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
            'start_date.after_or_equal' => 'La date de départ doit être aujourd\'hui ou plus tard.',
            'end_date.after_or_equal' => 'La date de retour doit être après la date de départ.',
            'reason.required' => 'La raison du déplacement est requise.',
            'status.prohibited' => 'Vous n\'avez pas la permission de modifier le statut.',
        ];
    }

    /**
     * Prepare the data for validation and sanitize it.
     */
    protected function prepareForValidation(): void
    {
        // Employés - définir user_id automatiquement
        if (auth()->user()->isEmployee()) {
            $this->merge([
                'user_id' => auth()->id(),
                'status' => 'pending',  // Employés ne peuvent que créer en pending
            ]);
        }
    }
}
