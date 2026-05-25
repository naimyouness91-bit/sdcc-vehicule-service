<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDemandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $demande = $this->route('demande') ?? $this->route('id');
        
        // Admins peuvent modifier toutes les demandes
        if (auth()->user()->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Employés ne peuvent modifier que leurs propres demandes (si pending)
        if ($demande && $demande->user_id === auth()->id() && $demande->status === 'pending') {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'destination' => 'nullable|string|max:255',
            'start_date' => 'nullable|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'kilometers' => 'nullable|integer|min:0',
            'distance_travelled' => 'nullable|integer|min:0',
            'reason' => 'nullable|string|max:1000',
        ];

        // Seuls les admins peuvent modifier le statut
        if (auth()->user()->hasRole(['admin', 'super_admin'])) {
            $rules['status'] = Rule::in(['pending', 'approved', 'rejected', 'cancelled']);
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'destination.max' => 'La destination ne peut pas dépasser 255 caractères.',
            'start_date.after_or_equal' => 'La date de départ doit être aujourd\'hui ou plus tard.',
            'end_date.after_or_equal' => 'La date de retour doit être après la date de départ.',
            'reason.max' => 'La raison ne peut pas dépasser 1000 caractères.',
        ];
    }
}
