<?php

namespace Tests\Feature\Controllers\Api\V1\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_login_successfully()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        // dd($response);
        //{"message":"Success","user":{"name":"Audreanne O'Kon DDS","email":"test@example.com","create_since":"1 second ago"},"token":"1|dVfoxvbIWhc0nw3vYUDCTinpqGvk1OPT3sT3dee59c81d0f0"}
        $response->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "user" => [
                    "name",
                    "email",
                    "create_since",
                ],
                "token"
            ]);
        $this->assertEquals("Success", $response->json('message'));
        $this->assertNotNull($response->json('token'));
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(403);
        $this->assertEquals("The provided credentials are incorrect.", $response->json('message'));
    }


    public function test_user_cannot_login_with_unregistered_email()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'anypassword',
        ]);

        $response->assertStatus(403);
    }


    public function test_user_can_register_successfully()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }


    public function test_user_cannot_register_with_duplicate_email()
    {
        $existingUser = User::factory()->create(['email' => 'duplicate@example.com']);

        $userData = [
            'name' => $this->faker->name,
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422);
    }


    public function test_authenticated_user_can_access_me_endpoint()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([

                'name',
                'email',
                'create_since'
            ])
            ->assertJson([
                'email' => auth()->user()->email // Assert that the returned user is the authenticated one
            ]);
    }

    public function test_unauthenticated_user_cannot_access_me_endpoint()
    {
        $response = $this->postJson('/api/v1/auth/me');

        $response->assertStatus(401); // 401 Unauthorized
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        //$this->assertCount(1, $user->tokens);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);

        $user->refresh();
        // $this->assertCount(0, $user->tokens);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401); // 401 Unauthorized
    }
}
