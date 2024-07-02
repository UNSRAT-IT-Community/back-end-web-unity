<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function insert(Request $request)
    {
        Role::create(['name' => $request->name]);
        return response()->json(['message' => 'Succesfully insert Role', 'data' => $request->name]);
    }
}
