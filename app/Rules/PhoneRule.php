<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validates E.164 and international phone number formats (e.g. +1234567890 or 0123456789)
        if (! is_string($value) || ! preg_match('/^\+?[1-9]\d{6,14}$/', preg_replace('/[\s\-\(\)]/', '', $value))) {
            $fail("The {$attribute} must be a valid phone number.");
        }
    }
}
