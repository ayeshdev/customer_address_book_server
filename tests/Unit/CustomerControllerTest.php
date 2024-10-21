<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        // Create a user and authenticate using Sanctum
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /** @test */
    public function it_returns_a_list_of_customers()
    {
        // Authenticate the user
        $this->authenticate();

        // Create 3 customers
        Customer::factory()->count(3)->create();

        // Send GET request to /api/customers
        $response = $this->getJson('/api/customers');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'company', 'contact', 'country', 'addresses']
                ]
            ]);
    }

    /** @test */
    public function it_creates_a_customer_with_addresses()
    {
        // Authenticate the user
        $this->authenticate();

        // Define customer data with addresses
        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'company' => 'Test Company',
            'contact' => '1234567890',
            'country' => 'Test Country',
            'addresses' => [
                ['no' => '123', 'street' => 'Main St', 'city' => 'Test City', 'state' => 'Test State']
            ]
        ];

        // Send POST request to create a customer
        $response = $this->postJson('/api/customers', $data);

        // Assert status, JSON structure, and database entries
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Customer']);
        $this->assertDatabaseHas('customers', ['name' => 'Test Customer']);
        $this->assertDatabaseHas('addresses', ['city' => 'Test City']);
    }

    /** @test */
    public function it_shows_a_customer_with_addresses()
    {
        // Authenticate the user
        $this->authenticate();

        // Create a customer with 2 addresses
        $customer = Customer::factory()->hasAddresses(2)->create();

        // Send GET request to show a customer by ID
        $response = $this->getJson("/api/customers/{$customer->id}");

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $customer->name])
            ->assertJsonStructure([
                'customer' => ['id', 'name', 'email'],
                'addresses' => ['*' => ['no', 'street', 'city', 'state']]
            ]);
    }

    /** @test */
    public function it_updates_a_customer_and_addresses()
    {
        // Authenticate the user
        $this->authenticate();

        // Create a customer with 2 addresses
        $customer = Customer::factory()->hasAddresses(2)->create();

        // Define updated customer data with updated address
        $data = [
            'name' => 'Updated Customer',
            'email' => $customer->email,
            'company' => 'Updated Company',
            'contact' => '9876543210',
            'country' => 'Updated Country',
            'addresses' => [
                ['id' => $customer->addresses->first()->id, 'no' => '456', 'street' => 'New St', 'city' => 'New City', 'state' => 'New State']
            ]
        ];

        // Send PUT request to update a customer
        $response = $this->putJson("/api/customers/{$customer->id}", $data);

        // Assert status, JSON structure, and database entries
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Customer']);
        $this->assertDatabaseHas('customers', ['name' => 'Updated Customer']);
        $this->assertDatabaseHas('addresses', ['city' => 'New City']);
    }

    /** @test */
    public function it_searches_for_customers()
    {
        // Authenticate the user
        $this->authenticate();

        // Create customers for search functionality
        Customer::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        Customer::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        // Send GET request to search for customers
        $response = $this->getJson('/api/customers?search=John');

        // Assert response status and that the search result contains 'John Doe'
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'John Doe']);
    }

    /** @test */
    public function it_deletes_a_customer()
    {
        // Authenticate the user
        $this->authenticate();

        // Create a customer
        $customer = Customer::factory()->create();

        // Send DELETE request to delete a customer by ID
        $response = $this->deleteJson("/api/customers/{$customer->id}");

        // Assert response status and JSON message
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Customer deleted successfully!']);
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
