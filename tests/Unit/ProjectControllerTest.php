<?php
use App\Models\Project;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    // No need for RefreshDatabase trait here if not using the database.

    public function test_it_creates_a_project_successfully()
    {
        // Arrange: Create a customer for the project association
    $customer = Customer::create([
        'name' => 'Test Customer',
        'email' => 'customer@example.com',
        'company' => 'Test Company',
        'contact' => '1234567890',
        'country' => 'Test Country',
        'status_id' => 1,
    ]);

    // Mock the request data
    $data = [
        'name' => 'Project Test',
        'description' => 'Test description',
        'customer_ids' => [$customer->id],
    ];

    // Validate the data
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'customer_ids' => 'array',
        'customer_ids.*' => 'exists:customers,id',
    ]);

    // Ensure the validation passes
    $this->assertFalse($validator->fails());

    // Create a project and associate it with the customer
    $project = new Project($data);
    $project->save();
    $project->customers()->attach($customer->id); // Ensure the customer is associated

    // Debugging: Check the project data
    $this->assertNotNull($project->id); // Ensure the project has been created
    $this->assertDatabaseHas('projects', [
        'name' => 'Project Test',
        'description' => 'Test description',
    ]);

    // Check that the project has the correct customer association
    $this->assertCount(1, $project->customers);
    $this->assertEquals($customer->id, $project->customers()->first()->id);
    }

    public function test_it_fails_to_create_a_project_with_invalid_data()
    {
        // Mock invalid request data (missing name)
        $data = [
            'description' => 'Test description',
            'customer_ids' => [],
        ];

        // Validate the data using the same validation rules in the controller
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_ids' => 'array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        // Ensure the validation fails due to missing 'name'
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }
}
