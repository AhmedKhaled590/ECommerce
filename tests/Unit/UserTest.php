<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_check_register_user_success()
    {
        $response = $this->post('/api/register', [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => 0,
            'address' => fake()->address,
            'phone_number' => "01144075825",
            'created_at' => now(),
            'updated_at' => now(),
            'state' => fake()->state,
            'city' => fake()->city,
        ]);
        $response->assertStatus(201);
    }

    public function test_check_register_user_fail_if_required_field_is_missed()
    {
        $attributes = ['name', 'email', 'password', 'password_confirmation', 'city', 'state', 'phone_number', 'address'];
        $data = [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => 0,
            'address' => fake()->address,
            'phone_number' => "01144075825",
            'created_at' => now(),
            'updated_at' => now(),
            'state' => fake()->state,
            'city' => fake()->city,
        ];
        //choose a random field to miss
        $field = $attributes[array_rand($attributes)];
        $data[$field] = null;
        $response = $this->post('/api/register', $data);
        $response->assertStatus(500);
    }

    public function test_check_login_success()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200);
    }

    public function test_check_login_with_wrong_password()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong password',
        ]);
        $response->assertStatus(404);
    }

    public function test_check_login_with_wrong_email_format()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => 'wrong email',
            'password' => 'password',
        ]);
        $response->assertStatus(500);
    }

    public function test_check_login_with_wrong_email()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => 'email@gmail.com',
            'password' => 'password',
        ]);
        $response->assertStatus(404);
    }

    public function test_check_if_response_is_json()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_check_if_response_has_message_field()
    {
        $urls = ['/api/login', '/api/register'];
        $user = User::factory()->create();
        $randChoice = rand(0, 10000) % 2;
        $dataRegister = [
            'name' => $randChoice == 0 ? fake()->name() : null,
            'email' => $randChoice == 0 ? fake()->safeEmail() : null,
            'email_verified_at' => now(),
            'password' => $randChoice == 0 ? 'password' : null,
            'password_confirmation' => $randChoice == 0 ? 'password' : null,
            'is_admin' => 0,
            'address' => $randChoice == 0 ? fake()->address : null,
            'phone_number' => $randChoice == 0 ? "01144075825" : null,
            'created_at' => now(),
            'updated_at' => now(),
            'state' => fake()->state,
            'city' => fake()->city,
        ];
        $dataLogin = [
            'email' => $randChoice == 0 ? $user->email : null,
            'password' => $randChoice == 0 ? 'password' : null,
        ];
        $data = [$dataLogin, $dataRegister];
        $response = $this->post($urls[$randChoice], $data[$randChoice]);
        $correlation_id = now()->toISOString();
        Log::channel('testLogs')->debug($correlation_id, [[
            'url' => $urls[$randChoice],
            'chosenData' => $data[$randChoice],
            'response' => $response->getContent(),
        ]]);
        $response->assertJson(fn(AssertableJson $response) => $response->has('message')->etc());
    }
}
