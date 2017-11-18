<?php

namespace App\Http\Controllers\Auth;

use App\Keys;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    use AuthenticatesUsers {
        sendLoginResponse as protected _sendLoginResponse;
    }

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
    }

    /**
     * Decrypt and add the user's private key to the session
     * to be used later.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function addKeys($request)
    {
        $private = Keys::decrypt(Auth::user()->private_key, $request['password']);
        session(['private_key' => $private]);
    }

    /**
     * Send the response after the user was authenticated.
     * Overridden to add the private key to the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendLoginResponse(Request $request)
    {
        $this->addKeys($request);
        return $this->_sendLoginResponse($request);
    }
}
