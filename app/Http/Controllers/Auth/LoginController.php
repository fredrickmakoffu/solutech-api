<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The provided credentials are incorrect.'
            ], 404);
        }
    
        $token = $user->createToken($request->email);
        $hours = config('sanctum.expiration')/60;

        return response()->json([
            'status' => true,
            'data' => array(
                'token' => $token->plainTextToken, 
                'expires_at' => Carbon::parse($token->accessToken->created_at)->addMinutes(config('sanctum.expiration'))->format('Y-m-d H:i')
            ),
            'message' => 'Token generated. Expiry in '.$hours.' hours.'
        ], 200);
    }
}