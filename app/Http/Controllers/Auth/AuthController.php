<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => "required|max:30",
            "email" => "required|email|unique:users,email",
            "password" => "required|confirmed|min:6"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        if (!$user) {
            return response()->json([
                "message" => "Register Fail!"
            ], 500);
        }

        return response()->json([
            "message" => "Register successfully",
        ], 200);
    }

    // =========== login ============== //
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|min:6"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $user = User::where("email", $request->email)->first();


        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ],400);
        }


        return response()->json([
            "token" => $user->createToken("Helloworld")->plainTextToken,
            "message" => "Login successfully"
        ] , 200);
    }
}
