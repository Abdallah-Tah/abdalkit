<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('badge_number', $request->badge_scan_number)
            ->orWhere('badge_scan_number', $request->badge_scan_number)->first();

        if ($user) {
            if ($user->password == null) {
                // redirect to password.set route with user id and request token
                return redirect()->route('password.view', ['id' => $user->id]);
            }
        }

        $request->authenticate();

        $request->session()->regenerate();

        // Check if the user has any roles
        // if ($user->roles->isEmpty()) {
        //     // If the user_roles table is empty, set the first user as admin
        //     if (UserRole::count() === 0) {
        //         $adminRole = Role::where('name', 'admin')->first();
        //         $user->roles()->attach($adminRole);
        //     } else {
        //         // Otherwise, assign the user as a guest
        //         $guestRole = Role::where('name', 'guest')->first();
        //         $user->roles()->attach($guestRole);
        //     }
        // }

        return redirect()->intended(RouteServiceProvider::HOME);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
