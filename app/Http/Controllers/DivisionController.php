<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Exceptions\UnexpectedField;
class DivisionController extends Controller
{
    public function insert(Request $request)
    {
        $expectedField = ['name'];
        $unexpectedFields = array_diff(array_keys($request->all()), $expectedField);
        if (!empty($unexpectedFields)) {
            throw new UnexpectedField($unexpectedFields);
        }
        $data = $request->only($expectedField);
        Division::create($data);
        return response()->json(['message' => 'Succesfully insert Division', 'data' => $data]);
    }
}
