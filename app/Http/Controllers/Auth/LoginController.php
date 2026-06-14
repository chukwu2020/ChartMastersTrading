<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use \Illuminate\Foundation\Auth\AuthenticatesUsers;

    /**
     * Show the login form.
     *
     * Redirect logged-in users to their dashboard immediately.
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            // Redirect already logged-in users
            return redirect()->route('user_dashboard');
        }

        return view('auth.login');
    }

    /**
     * Override the login method to safely logout old session if already logged in.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If user is already logged in, logout old session & invalidate
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Attempt login using trait method
        if ($this->attemptLogin($request)) {
            // Regenerate session after successful login to prevent fixation
            $request->session()->regenerate();

            return $this->sendLoginResponse($request);
        }

        // Failed login response
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Handle what happens after successful authentication.
     */
    protected function authenticated(Request $request, $user)
    {
        // 🔒 Check if user needs to complete registration (Step 2)
        if ($user->registration_step < 2 || $user->account_status === 'pending') {
            // Store user ID in session for the additional info page
            session()->put('registration_user_id', $user->id);
            
            // Logout the user
            Auth::logout();
            
            // ✅ DO NOT invalidate the entire session - just logout auth
            // The session still exists with our registration_user_id
            
            // Redirect to additional info page with a clear message
            return redirect()->route('user.additional.info')
                ->with('warning', 'Please complete your trading profile to activate your account.');
        }

        // ✅ User is fully registered - proceed with normal login flow
        $today = now()->toDateString();

        if (session('overlayDate') !== $today) {
            session([
                'overlayDate' => $today,
                'overlayCount' => 0,
            ]);
        }

        $overlayCount = session('overlayCount', 0);

        if ($overlayCount < 2 && !session()->has('overlayShownThisLogin')) {
            session([
                'showTradingOverlay' => true,
                'overlayShowAt' => now()->addSeconds(40)->timestamp,
                'overlayCount' => $overlayCount + 1,
                'overlayShownThisLogin' => true,
            ]);
        } else {
            session()->forget('showTradingOverlay');
        }

        // ✅ Allow valid users
        return redirect()->route(
            $user->role_as == 1 ? 'admin_dashboard' : 'user_dashboard'
        );
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}