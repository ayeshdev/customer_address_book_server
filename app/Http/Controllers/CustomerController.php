<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('addresses')->get();
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:customers',
            'company' => 'required|string',
            'contact' => 'required|string',
            'country' => 'required|string',
            'addresses' => 'array|min:1', // Ensure at least one address
            'addresses.*.no' => 'required|string', // Validate 'no' field for each address
            'addresses.*.street' => 'required|string', // Validate 'street' field for each address
            'addresses.*.city' => 'required|string', // Validate 'city' field for each address
            'addresses.*.state' => 'required|string', // Validate 'state' field for each address
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company' => $validated['company'],
            'contact' => $validated['contact'],
            'country' => $validated['country'],
            'status_id' => 1,
        ]);

        // Loop through each address and associate it with the customer
        foreach ($validated['addresses'] as $addressData) {
            $customer->addresses()->create([
                'no' => $addressData['no'],
                'street' => $addressData['street'],
                'city' => $addressData['city'],
                'state' => $addressData['state'],
            ]);
        }

        return new CustomerResource($customer);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {

       $addresses =  Address::where('customer_id',$customer->id)->get();

        return [
            'customer'=>$customer,
            'addresses'=>$addresses
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|exists:customers',
        'company' => 'required|string',
        'contact' => 'required|string',
        'country' => 'required|string',
        'addresses' => 'array|min:1', // Ensure at least one address
        'addresses.*.no' => 'required|string',
        'addresses.*.street' => 'required|string',
        'addresses.*.city' => 'required|string',
        'addresses.*.state' => 'required|string',
    ]);

    // Update customer basic details
    $customer->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'company' => $validated['company'],
        'contact' => $validated['contact'],
        'country' => $validated['country'],
        'status_id' => 1,
    ]);

    // Get current customer addresses
    $existingAddresses = $customer->addresses->keyBy('id');

    // Loop through each address in the request
    foreach ($validated['addresses'] as $addressData) {
        if (isset($addressData['id']) && $existingAddresses->has($addressData['id'])) {
            // Update existing address
            $existingAddresses[$addressData['id']]->update([
                'no' => $addressData['no'],
                'street' => $addressData['street'],
                'city' => $addressData['city'],
                'state' => $addressData['state'],
            ]);
            // Remove the address from the collection (mark it as handled)
            $existingAddresses->forget($addressData['id']);
        } else {
            // Create a new address
            $customer->addresses()->create([
                'no' => $addressData['no'],
                'street' => $addressData['street'],
                'city' => $addressData['city'],
                'state' => $addressData['state'],
            ]);
        }
    }

    // Delete remaining addresses that were not in the request (addresses removed by the user)
    foreach ($existingAddresses as $address) {
        $address->delete();
    }

    return new CustomerResource($customer->load('addresses'));
}

public function searchCustomer(Request $request){
    // Check if the search query exists in the request
    $searchQuery = $request->query('search');

    // If there's a search query, filter the customers, otherwise return all customers
    if ($searchQuery) {
        $customers = Customer::where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('email', 'LIKE', '%' . $searchQuery . '%')
            ->get();
    } else {
        $customers = Customer::all();
    }

    // Return the customers in a JSON response
    return response()->json([
        'customers' => $customers
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Delete the customer
    $customer->delete();

    // Return a JSON response indicating the deletion was successful
    return response()->json([
        'message' => 'Customer deleted successfully!'
    ], 200);
    }
}
