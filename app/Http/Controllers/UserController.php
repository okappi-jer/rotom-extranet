<?php

namespace App\Http\Controllers;

use App\Mail\NewUser;
use App\Models\Lotnumber;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Custom\CreatioComm\CreatioComm;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($guid){
        $users = User::get();

        return response()->json([
            'data' => $users,
        ], 200);
    }

    public function getLotnumberInfo()
    {
        $last_lotnumber = Lotnumber::latest()->first();
        $new_lotnumber = str_replace('E', '', $last_lotnumber->lotnumber) + 1;

        return response()->json([
            'last_lotnumber' => $last_lotnumber->lotnumber,
            'new_lotnumber' => 'E' . $new_lotnumber
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
            'firstname' => ['required', 'max: 256'],
            'email' => ['required', 'unique:users', 'email'],
            'role' => ['required', 'max: 256'],
            'supplier_code' => ['required', 'max: 256'],
            'password' => ['required', 'max: 256'],
            'company'=> ['required', 'max: 256'],
            'delivers_to'=> ['required', 'max: 256'],
        ]);

        //Update lotnumber
        $last_lotnumber = Lotnumber::latest()->first();
        $new_lotnumber = str_replace('E', '', $last_lotnumber->lotnumber) + 1;

        Lotnumber::create([
            'lotnumber' => 'E' . $new_lotnumber,
        ]);

        $user =  User::create([
            'name' => $request['name'],
            'firstname' => $request['firstname'],
            'email' => $request['email'],
            'role' => $request['role'],
            'password' => Hash::make($request['password']),
            'password_plain' => $request['password'],
            'supplier_code' => $request['supplier_code'],
            'company' => $request['company'],
            'delivers_to' => $request['delivers_to'],
            'lotnumber' => 'E' . $new_lotnumber,
        ]);

        //Create user in Laravel
        if($user){
            //Mail::to($user->email)->send(new NewUser($user));

            return response()->json([
                'contact' => $user,
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
        $user = User::where('id', $guid)->first();

        //Check if user exists
        if($user){
            //Update password? Or only name/first name?
            if(empty($request['password'])){
                $request->validate([
                    'name' => ['required', 'max: 256'],
                    'firstname' => ['required', 'max: 256'],
                    'supplier_code' => ['required', 'max: 256'],
                    'company'=> ['required', 'max: 256'],
                ]);

                $user->update([
                    'name' => $request['name'],
                    'firstname' => $request['firstname'],
                    'supplier_code' => $request['supplier_code'],
                    'company' => $request['company'],
                ]);
            }else{
                $request->validate([
                    'name' => 'required',
                    'firstname' => 'required',
                    'supplier_code' => ['required', 'max: 256'],
                    'password' => ['required', 'max: 256'],
                    'company' => ['required', 'max: 256'],
                ]);

                $user->update([
                    'name' => $request['name'],
                    'firstname' => $request['firstname'],
                    'password' => Hash::make($request['password']),
                    'password_plain' => $request['password'],
                    'supplier_code' => $request['supplier_code'],
                    'company' => $request['company'],
                ]);
            }

            //Return response
            return response()->json([
                'data' => "User updated successfully!"
            ], 200);

        }else{
            return response()->json([
                'data' => "Something went wrong, try again later!"
            ], 401);
        }
    }

    public function delete($guid){
        $user = User::where('id', $guid)->first();

        // DESTROY USER
        User::destroy($user->id);

        // RETURN RESPONSE
        return response()->json([
            'data' => 'User deleted successfully'
        ], 200);
    }
}
