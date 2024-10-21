<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase; // Ensure database is refreshed between tests

    public function test_it_registers_a_user_with_valid_data()
    {
        // Arrange: Prepare valid data
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Act: Simulate the registration logic
        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Assert: Check if validation passes
        $this->assertFalse($validation->fails());

        // Create user in the database
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assert: Check that the user has the correct attributes
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password)); // Verify hashed password
    }

    public function test_it_fails_registration_if_email_already_exists()
    {
        // Arrange: Create an existing user
        User::create([
            'name' => 'Existing User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act: Attempt to register with the same email
        $data = [
            'name' => 'New User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Validate the data
        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Assert: Validation error is returned for duplicate email
        $this->assertTrue($validation->fails());
        $this->assertArrayHasKey('email', $validation->errors()->messages());
    }

    public function test_it_fails_registration_if_password_confirmation_does_not_match()
    {
        // Act: Attempt to register with mismatching passwords
        $data = [
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => 'password',
            'password_confirmation' => 'not_matching_password',
        ];

        // Validate the data
        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Assert: Validation error is returned for password confirmation
        $this->assertTrue($validation->fails());
        $this->assertArrayHasKey('password', $validation->errors()->messages());
    }
}
