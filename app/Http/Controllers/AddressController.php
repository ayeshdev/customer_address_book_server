<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function destroy(Address $address)
    {
        $address->delete();
        return ["message"=>"Address is deleted!"];
    }
}
