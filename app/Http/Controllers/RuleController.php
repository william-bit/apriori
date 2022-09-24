<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index(Request $request)
    {

        $this->validate($request, [
            'confidence' => 'required|numeric|gt:0|lt:1',
            'support' => 'required|numeric|gt:0|lt:1',
        ]);
        Rule::updateOrCreate(
            ['id' => '1'],
            ['confidence' => $request->confidence, 'support' => $request->support]
        );
        return 'true';
    }
    public function getStatus()
    {
        return Rule::find(1);
    }
}
