<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SanitizedTextRule implements ValidationRule
{
    /**
     * Run the validation rule to prevent XSS script tags and harmful HTML injections.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('/<script[\s\S]*?>[\s\S]*?<\/script>/i', $value)) {
            $fail("The {$attribute} contains unallowed HTML or script tags.");
        }
    }
}
