<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SetPasswordController extends Controller
{
    //setPassword a new password for the user and redirect to login page with success message
    public function setPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::find($id);

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Password set successfully. Please login to continue.');
        } else {
            return redirect()->route('login')->withErrors(['user' => 'User not found']);
        }
    }
}
