<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        UserLoginAttempt::create([
            'user_id' => $user->id,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect()->route('dashboard')
        ->withSuccess('You have successfully registered & logged in!');
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $findUserID = User::where('email',$request->email)->first();
        $ipadd = $_SERVER['REMOTE_ADDR'];
        $record = UserLoginAttempt::where('user_id','=',$findUserID->id)->where('ip_address','=',$ipadd)->first();
        if(!empty($record) && $record->user_id == $findUserID->id){
            //return back()->withErrors([
            //    'message' => 'You are already loggedIn.Try clearing old session and retry',
            //]);
            if($request->confirmed == 1){
                Auth::setUser($findUserID)->logoutOtherDevices($request->get('password'));
                $res = UserLoginAttempt::where('user_id',$findUserID->id)->delete();

            }else {
                return redirect()->back()->with('error', 'You are already loggedin');
            }

        }
        if(Auth::attempt($credentials))
        {
            UserLoginAttempt::create([
                'user_id' => Auth::id(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

                $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');

        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    }

    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::check())
        {
            return view('auth.dashboard');
        }

        return redirect()->route('login')
            ->withErrors([
            'email' => 'Please login to access the dashboard.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $id = Auth::id();
        $record = UserLoginAttempt::where('user_id',$id)->first();

        if(!empty($record) && $record['user_id']  !== null){
            $record->delete();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }

}
