<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Rules\PhoneRule;
use App\Rules\SanitizedTextRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_status_api_endpoint(): void
    {
        $response = $this->getJson('/api/v1/status');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'operational',
                ],
            ]);
    }

    public function test_sanctum_api_authentication_flow(): void
    {
        $school = School::create([
            'name' => 'Test School',
            'code' => 'TEST-01',
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'token_type', 'user'],
            ]);

        $token = $loginResponse->json('data.token');

        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'admin@test.com',
                    ],
                ],
            ]);
    }

    public function test_phone_validation_rule(): void
    {
        $validValidator = Validator::make(['phone' => '+15559876543'], ['phone' => new PhoneRule]);
        $this->assertFalse($validValidator->fails());

        $invalidValidator = Validator::make(['phone' => 'abc-not-a-phone'], ['phone' => new PhoneRule]);
        $this->assertTrue($invalidValidator->fails());
    }

    public function test_sanitized_text_rule(): void
    {
        $cleanValidator = Validator::make(['text' => 'Normal school description'], ['text' => new SanitizedTextRule]);
        $this->assertFalse($cleanValidator->fails());

        $xssValidator = Validator::make(['text' => '<script>alert("xss")</script>'], ['text' => new SanitizedTextRule]);
        $this->assertTrue($xssValidator->fails());
    }
}
