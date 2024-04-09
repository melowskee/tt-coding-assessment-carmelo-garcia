<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'fullname' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/photos', $photoName);
        }

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'fullname' => $request->fullname,
            'password' => bcrypt($request->password),
            'photo' => isset($photoName) ? $photoName : null,
        ]);

        //return response()->json(['user' => $user, 'message' => 'User registered successfully'], 201);
        $token = $user->createToken('token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json(['user' => $user, 'access_token' => $token], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}