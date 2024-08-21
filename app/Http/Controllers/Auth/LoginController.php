<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        // Set the session flag to show the modal
        session()->put('show_modal', true);
    }
    public function logout(Request $request)
    {
        // dd($request->session()->all());
        // Clear the modal session on logout
        $this->guard()->logout();
        $request->session()->forget('show_modal');
        // Optional: Clear any other session data you want
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to your desired location after logout
    }
}
