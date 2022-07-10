<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        //create password rule
        try {
            $request->validate([
                'email' => 'email|required|unique:users,email',
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:11|starts_with:01',
                'address' => 'required|string|max:255',
                'state' => 'string|max:255',
                'city' => 'required|string|max:255',
                'password' => ['required', Password::defaults()],
                'password_confirmation' => 'required|same:password',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'password' => bcrypt($request->password),
            ]);

            $tempFile = TemporaryFile::where('folder', $request->avatar)->first();
            $user->addMedia(storage_path('app/public/avatars/tmp/' . $tempFile->folder . '/' . $tempFile->filename))
                ->toMediaCollection('avatars');
            rmdir(storage_path('app/public/avatars/tmp/' . $tempFile->folder));
            $tempFile->delete();

            event(new Registered($user));
            auth()->login($user);
            return response()->json(['user' => $user, 'message' => 'User Created Successfully'], 201);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
}
