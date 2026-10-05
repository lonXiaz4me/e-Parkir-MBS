<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class MalaysianIcNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Malaysian IC format: YYMMDD-PB-###G (12 digits)
        if (!preg_match('/^\d{6}-?\d{2}-?\d{4}$/', $value)) {
            $fail('The :attribute must be a valid Malaysian IC number.');
        }
    }
}