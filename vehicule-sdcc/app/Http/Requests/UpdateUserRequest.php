<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\OptionsService;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is checked by the controller using policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $optionsService = app(OptionsService::class);
        $isSuperAdmin = auth()->user()?->hasRole('super_admin') ?? false;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'service' => ['required', Rule::in(array_keys($optionsService->userServices()))],
            'role' => ['required', Rule::in(array_keys($optionsService->userAssignableRoles($isSuperAdmin)))],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'name.required' => 'Le nom est obligatoire.',
            'role.in' => 'Le rôle sélectionné est invalide.',
        ];
    }
}
