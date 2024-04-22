<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Custom\CreatioComm\CreatioComm;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $employees = Employee::get();

        return response()->json([
            'data' => $employees,
        ], 200);
    }

    /**
     * Post new user to Laravel + Creatio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //Validate request
        $request->validate([
            'name' => ['required', 'max: 256'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'max: 256'],
        ]);

        
        $employee =  Employee::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'mobile' => $request['mobile'],
        ]);

        //Create user in Laravel
        if($employee){
            return response()->json([
                'employee' => $employee,
            ], 200);
        }else{
            return response()->json([
                'data' => "Er ging iets mis, probeer later even opnieuw!"
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $guid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $guid)
    {
        //Find user in local DB
        $employee = Employee::where('id', $guid)->first();

        //Check if user exists
        if($employee){
            $request->validate([
                'name' => ['required', 'max: 256'],
                'email' => ['required', 'max: 256'],
                'mobile' => ['required', 'max: 256'],
            ]);

            $employee->update([
                'name' => $request['name'],
                'email' => $request['email'],
                'mobile' => $request['mobile'],
            ]);

            //Return response
            return response()->json([
                'data' => "Employee updated successfully!"
            ], 200);

        }else{
            return response()->json([
                'data' => "Something went wrong, try again later!"
            ], 401);
        }
    }

    public function delete($guid){
        $employee = Employee::where('id', $guid)->first();

        // DESTROY USER
        Employee::destroy($employee->id);

        // RETURN RESPONSE
        return response()->json([
            'data' => 'Employee deleted successfully'
        ], 200);
    }
}
