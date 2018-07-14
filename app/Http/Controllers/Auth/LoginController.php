<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Token;
use App\Models\User;

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
    }

    public function login(Request $request) {
        $this->validateLogin($request);

        if ($user = app('auth')->getProvider()->retrieveByCredentials($request->only('email', 'password'))) {
            if($user->tfa == 'sms' || $user->tfa == 'voice') {
                $token = Token::create([
                    'user_id' => $user->id
                ]);
                if ($token->sendCode($user->tfa)) {
                    $request->session()->put("token_id", $token->id);
                    $request->session()->put("user_id", $user->id);
                    $request->session()->put("remember", $request->get('remember'));

                    return redirect()->route('otpVerify');
                }

                $token->delete();// delete token because it can't be sent
                return redirect('/login')->withErrors([
                    "Unable to send verification code"
                ]);
            } else if($user->tfa == "software") {
                $request->session()->put("user_id", $user->id);
                $request->session()->put("remember", $request->get('remember'));
                return redirect()->route("tokenVerify");
            } else {
                $this->guard()->login($user);
                return redirect()->route('home');
            }
        } else {
            return redirect('/login')->withErrors([
                "email" => "These credentials do not match our records."
            ]);
        }
    }

    public function otpVerify(Request $request) {
        if (!$request->session()->has("token_id")) {
            return redirect("login");
        }
        return view('auth.otp');
    }

    public function handleOtpVerify(Request $request) {
        if (!$request->session()->has("token_id", "user_id")) {
            return redirect("login");
        }
        $token = Token::find($request->session()->get("token_id"));
        if (! $token || ! $token->isValid() || $request->code !== $token->code ||
            (int) $request->session()->get("user_id") !== $token->user->id
        ) {
            return redirect()->route("otpVerify")->withErrors(["Invalid code"]);
        }
        $token->used = true;
        $token->save();
        $this->guard()->login($token->user, $request->session()->get('remember', false));
        $request->session()->forget('token_id', 'user_id', 'remember');

        return redirect('home');
    }

    public function tokenVerify(Request $request) {
        if (!$request->session()->has("user_id")) {
            return redirect("login");
        }
        $user = User::find($request->session()->get("user_id"));
        if(empty($user)) {
            return redirect('/login')->withErrors([
                "User not found !"
            ]);
        }
        if(empty($user->google2fa_secret)) {
            return redirect('/login')->withErrors([
                "Token not found !"
            ]);
        }
        return view('auth.verify', ['QRImage' => app('pragmarx.google2fa')->getQRCodeInline(env('APP_NAME'), $user->email, decrypt($user->google2fa_secret))]);
    }

    public function handleTokenVerify(Request $request) {
        if (!$request->session()->has("user_id")) {
            return redirect("login");
        }
        $user = User::find($request->session()->get("user_id"));

        $token = $request->input('token-code');

        $valid = app('pragmarx.google2fa')->verifyKey(decrypt($user->google2fa_secret), $token);
        if($valid) {
            $request->session()->forget("user_id");
            $this->guard()->login($user);
            return redirect()->route('home');
        } else {
            return redirect()->route('tokenVerify')->withErrors([
                "Token fail !"
            ]);
        }
    }
}
