<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Register User
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request) { // store

        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8",
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',

        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }

        try {

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
    
            // if has avatar image
            if($request->file('avatar')) {
                $image_path = $request->file('avatar')->store('avatars', 'public');
                $user['avatar'] = $image_path;
                $user->save();
            }
    
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! User registered.", 
                "data" => new UserResource($user),
            ], 201);

        } catch(Exception $exception) {

            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "message" => $exception->getMessage(),
            ], 404);
        }

    }

    /**
     * User Login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }

        try {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $token = $user->createToken('token')->accessToken;
                return response()->json([
                    "status" => "success",
                    "error" => false,
                    "message" => "Success! you are logged in.",
                    "token" => $token
                ], 200);
            }
            return response()->json([
                "status" => "failed", 
                "message" => "Failed! invalid credentials."
            ], 404);

        } catch(Exception $exception) {

            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "message" => $exception->getMessage(),
            ], 404);
        }
    }

    /**
     * Logged User Data Using Auth Token
     *
     * @return void
     */
    public function profile() {
        try {
            $user = Auth::user();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "data" => new UserResource($user)
            ], 200);

        } catch(NotFoundHttpException $exception) {
            return response()->json([
                "status" => "failed", 
                "error" => $exception->getMessage(),
            ], 401);
        }
    }

    /**
    * Logout Auth User
    *
    * @param Request $request
    * @return void
    */
    public function logout() {

        if(Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! You are logged out."
            ], 200);
        }
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed! You are already logged out."
        ], 403);
    }
}


