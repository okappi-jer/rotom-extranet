<?php

namespace App\Http\Controllers;

use App\Models\Gatecontrol;
use Illuminate\Http\Request;
use App\Imports\GatecontrolImport;

class GatecontrolController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //Validate request
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        \Excel::import(new GatecontrolImport, $request['file']);

        return response()->json([
            'data' => "Succes",
        ], 200);

    }
}
