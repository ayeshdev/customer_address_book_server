<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_it_allows_a_user_with_valid_credentials_to_login()
    {
        // Arrange: Create a mock user
        $user = new User([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act: Simulate login logic
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $validation = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if validation fails
        $this->assertFalse($validation->fails());

        // Check if credentials are valid
        $this->assertEquals($user->email, $credentials['email']);
        $this->assertTrue(Hash::check($credentials['password'], $user->password));
    }

    public function test_it_rejects_login_when_email_is_invalid()
    {
        // Act: Attempt to login with an invalid email
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ];

        // Validate the credentials
        $validation = Validator::make($credentials, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);

        // Assert: Check if validation fails
        $this->assertTrue($validation->fails());
        $this->assertArrayHasKey('email', $validation->errors()->messages());
    }

    public function test_it_rejects_login_when_password_is_incorrect()
    {
        // Arrange: Create a mock user
        $user = new User([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act: Attempt login with incorrect password
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Check if credentials are valid
        $this->assertEquals($user->email, $credentials['email']);
        $this->assertFalse(Hash::check($credentials['password'], $user->password)); // Incorrect password should fail
    }

    public function test_it_requires_email_and_password_for_login()
    {
        // Act: Attempt login without email and password
        $credentials = [];

        // Validate the credentials
        $validation = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Assert: Check if validation fails
        $this->assertTrue($validation->fails());
        $this->assertArrayHasKey('email', $validation->errors()->messages());
        $this->assertArrayHasKey('password', $validation->errors()->messages());
    }
}
