<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    //Google Callback
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        //print_r($user);die;

        $this->registerOrLoginUser($user);
        //redirected to home page after login
        return redirect()->route('home');
    }

    //Register if New User
    protected function registerOrLoginUser($data)
    {
        $user = User::where('email','=',$data->email)->first();
        //print_r($user); die;
        if(!$user)
        {
            $user = new User();
            $user->name     = $data->name;
            $user->email    = $data->email;
            $user->provider_id = $data->id;
            $user->avatar   = $data->avatar;
            $user->save();
        }

        Auth::login($user);
    }
}
