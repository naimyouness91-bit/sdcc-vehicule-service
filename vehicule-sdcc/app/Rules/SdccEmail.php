<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SdccEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Ensures the email address ends with @sdcc.ma.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!str_ends_with(strtolower(trim($value)), '@sdcc.ma')) {
            $fail('Seules les adresses email avec @sdcc.ma sont autorisées.');
        }
    }
}
