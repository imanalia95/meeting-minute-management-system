<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SimplePasswordResetController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.exists' => 'Tiada akaun didaftarkan dengan emel ini.',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('login')->with('status', 'Kata laluan berjaya ditukar. Sila log masuk.');
    }
}