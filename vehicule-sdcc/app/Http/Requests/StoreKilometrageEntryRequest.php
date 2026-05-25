<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKilometrageEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admins and super_admins can record mileage entries.
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
            'car_id' => 'required|integer|exists:cars,id',
            'entry_date' => 'required|date|before_or_equal:today',
            'kilometers' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'car_id.required' => 'Le véhicule est requis.',
            'car_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'entry_date.required' => 'La date est requise.',
            'entry_date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'kilometers.required' => 'Le kilométrage est requis.',
            'kilometers.integer' => 'Le kilométrage doit être un nombre entier.',
            'kilometers.min' => 'Le kilométrage ne peut pas être négatif.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
        ];
    }
}
