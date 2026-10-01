<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider Driver
    |--------------------------------------------------------------------------
    |
    | Options: 'local_heuristic', 'openai', 'anthropic', 'gemini'
    |
    */
    'default' => env('AI_PROVIDER', 'local_heuristic'),

    /*
    |--------------------------------------------------------------------------
    | Usage & Token Quota Limits
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'daily_requests_per_user' => env('AI_DAILY_REQUEST_LIMIT', 100),
        'daily_tokens_per_user' => env('AI_DAILY_TOKEN_LIMIT', 100000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Credentials & Settings
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY', ''),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY', ''),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY', ''),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
        ],
    ],
];
