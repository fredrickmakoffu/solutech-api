<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    
    protected function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'min:8',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->errors()
            ], 400);
        } else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
            ]);
            
            if($user) {
                // event(new Registered($user));

                return response()->json([
                    'status' => true,
                    'data' => array(
                        'name' => $user->name,
                        'email' => $user->email
                    )
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not created.'
                ], 500);
            }
        }
    }

    protected function verify(EmailVerificationRequest $request) {
        $request->fulfill();

        return response()->json([
            'status' => true,
            'message' => 'Account Verified'
        ], 200);
    }

    protected function resendVerification(Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification Email resent'
        ], 200);
    }

    protected function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email']);
    
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'status' => true,
                'message' => $status
            ], 200)
            : response()->json([
                'status' => false,
                'message' => $status
            ], 500);
    }

    protected function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        return $status == Password::PASSWORD_RESET
            ? response()->json([
                'status' => true,
                'message' => $status
            ], 200)
            : response()->json([
                'status' => false,
                'message' => $status
            ], 500);
    }
}
