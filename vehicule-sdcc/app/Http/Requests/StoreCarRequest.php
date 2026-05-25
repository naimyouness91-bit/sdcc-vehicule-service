<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'matricule' => 'required|string|unique:cars,matricule|max:20',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|in:disponible,indisponible,maintenance',
            'availability_type' => 'required|in:both,weekend',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du véhicule est requis.',
            'matricule.required' => 'La matricule est requise.',
            'matricule.unique' => 'Cette matricule existe déjà.',
            'brand.required' => 'La marque est requise.',
            'model.required' => 'Le modèle est requis.',
            'year.required' => 'L\'année est requise.',
            'status.required' => 'Le statut est requis.',
            'availability_type.required' => 'Le type de disponibilité est requis.',
        ];
    }
}
