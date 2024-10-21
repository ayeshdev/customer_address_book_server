<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('customers')->get();
        // Return the data using the ProjectResource
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        \Log::info("request" . " " . $request);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_ids' => 'array', // Ensure customer_ids is an array
            'customer_ids.*' => 'exists:customers,id', // Ensure each customer ID exists in the customers table
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Create a new project instance and fill it with validated data
        $project = new Project($request->only('name', 'description'));

        // Save the project to the database
        $project->save();

        // If there are customers associated with the project, attach them
        if ($request->has('customer_ids')) {
            // Attach customers using the customer IDs provided in the request
            $project->customers()->attach($request->input('customer_ids'));
        }

        // Return a JSON response with the created project data
        return response()->json([
            'message' => 'Project created successfully!',
            'project' => $project,
        ], 201); // 201 Created status
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        $project->load('customers');

        \Log::info("project" . " " . $project);
        return [
            'project' => $project,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_ids' => 'array', // Ensure customer_ids is an array
            'customer_ids.*' => 'exists:customers,id', // Ensure each customer ID exists in the customers table
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Update the project with validated data
        $project->update($request->only('name', 'description'));

        // If there are customers associated with the project, sync them
        if ($request->has('customer_ids')) {
            // Sync customers using the customer IDs provided in the request
            $project->customers()->sync($request->input('customer_ids'));
        }

        // Return a JSON response with the updated project data
        return response()->json([
            'message' => 'Project updated successfully!',
            'project' => new ProjectResource($project->load('customers')), // Load customers relation
        ], 200); // 200 OK status
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Detach all customers linked to the project before deletion
        $project->customers()->detach();

        // Delete the project
        $project->delete();

        // Return a JSON response indicating the deletion was successful
        return response()->json([
            'message' => 'Project deleted successfully!'
        ], 200);
    }
}
