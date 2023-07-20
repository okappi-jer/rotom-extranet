<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request['email'])->first();

        if(!$user){
            return response()->json([
                'message' => 'Email does not exist.'
            ], 422);
        } else {
            $this->sendMail($user->email);

            return response()->json([
                'message' => 'Bedankt! Een link om uw wachtwoord opnieuw in te stellen werd zonet naar uw e-mailadres verzonden.'
            ], 200);
        }
    }

    public function sendMail($email)
    {
        $token = $this->generateToken($email);
        Mail::to($email)->send(new SendMail($token));
    }

    public function generateToken($email)
    {
        $isOtherToken = DB::table('password_resets')->where('email', $email)->first();

        if($isOtherToken) {
            return $isOtherToken->token;
        }

        $token = Str::random(80);
        $this->storeToken($token, $email);

        return $token;
    }

    public function storeToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function passwordResetProcess(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ]);

        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }

    private function updatePasswordRow($request)
    {
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token
        ]);
    }

    private function tokenNotFoundError()
    {
        return response()->json([
            'error' => 'Either your email or your token is wrong.'
        ], 422);
    }

    private function resetPassword($request)
    {
        $user = User::where('email', $request->email)->first();

        $user->update([
            'password' => Hash::make($request['password']),
        ]);

        $this->updatePasswordRow($request)->delete();

        return response()->json([
            'data'=>'Password has been updated.'
        ],200);
    }
}
