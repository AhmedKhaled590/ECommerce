<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email|max:255',
                'password' => ['required', Password::defaults()],
            ]);
            $user = User::where('email', $credentials['email'])->first();
            if (!$user) {
                return response()->json(['message' => 'Invalid Email'], 401);

            }
            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Invalid Password'], 401);
            }
            $token = $user->createToken('myapptoken', ['remember'])->plainTextToken;
            return response()->json(['message' => 'Login Successful', 'token' => $token], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
