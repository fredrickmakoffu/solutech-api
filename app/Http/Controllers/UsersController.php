<?php

namespace App\Http\Controllers;

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

class UsersController extends Controller
{
    
    protected function index() {
        $headers = ['name', 'email'];

        $users = User::select(['name', 'email', 'id'])->get();

        return response()->json([
            'data' => $users,
            'totals' => $users->count(),
            'headers' => $headers
        ], 200);
    }

    public function showByToken(Request $request) {
        $user = $request->user();
        
        if( !$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated!'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    protected function update($id, Request $request) {
        $user = User::find($id);    

        if($user) {
            if ($request->input('name') && $user->name != $request->input('name')) { 
                $user->name = $request->input('name');
            } else if ($request->input('email') && $user->name != $request->input('email')) { 
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email|max:255|unique:users',
                ]);

                if($validator->fails()) {   
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()
                    ], 400); 
                } else {
                    $user->email = $request->input('email');
                }
            } else if ($request->input('password') && $user->password != $request->input('password')) { 
                $validator = Validator::make($request->all(), [
                    'old_password' => 'required|max:255',
                ]);

                if(Hash::check($request->old_password, $user->password)) {  
                    $user->password =  Hash::make($request->input('password'));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have not provided your current correct password.'
                    ], 400);
                }  
                
            }

            $save = $user->save();

            if($save) {
                return response()->json([
                    'status' => true,
                    'message' => 'Details updated.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Details not updated.'
                ], 500); 
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No user found.'
            ], 404); 
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
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
                'password' => bcrypt('changemetafadhali')
            ]);
            
            if($user) {
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
    
    public function delete($id) {
        $user = User::find($id);

        if($user) {
            $deleted = $user->delete();

            if($deleted) {
                return response()->json([
                    'status' => true,
                    'message' => 'User deleted.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not deleted.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }
}
