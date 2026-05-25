<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\OptionsService;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = auth()->user();
        if (!$actor) {
            return false;
        }

        // Delegate to policy for consistency with controller
        return $actor->can('create', \App\Models\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $optionsService = app(OptionsService::class);
        $isSuperAdmin = auth()->user()?->hasRole('super_admin') ?? false;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'service' => ['required', Rule::in(array_keys($optionsService->userServices()))],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Require at least one lowercase, one uppercase and one digit
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/',
            ],
            'role' => ['required', Rule::in(array_keys($optionsService->userAssignableRoles($isSuperAdmin)))],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.unique' => 'Cet email existe déjà.',
            'service.required' => 'Le service est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle est requis.',
        ];
    }
}
