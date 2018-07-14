<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $user = User::find(Auth::user()->id);
        $google2fa = app('pragmarx.google2fa');
        $data = ['user' => $user, 'types' => User::TFA];
        if($user->twa != 'software' && empty($user->google2fa_secret)) {
            $google2faSecret = $google2fa->generateSecretKey();
            $request->session()->flash('google2faSecret', $google2faSecret);
        } else {
            $google2faSecret = decrypt($user->google2fa_secret);
        }
        $data['QRImage'] = $google2fa->getQRCodeInline(env('APP_NAME'), $user->email, $google2faSecret);
        $data['google2faSecret'] = $google2faSecret;
        return view('auth.setting', $data);
    }

    public function store(Request $request) {
        $status = false;
        $user = User::find(Auth::user()->id);
        $google2fa = app('pragmarx.google2fa');
        $data = ['user' => $user, 'types' => User::TFA];
        if($request->has('2fa') && array_key_exists($request->input('2fa'), User::TFA)) {
            $user->tfa = $request->input('2fa');
            if($request->input('2fa') == "software" && $request->has('google-2fa-secret')) {
                $user->google2fa_secret = encrypt($request->input('google-2fa-secret'));
            }
            $status = $user->save();
            $data['status'] = $status;
        }

        if(!empty($user->google2fa_secret)) {
            $google2faSecret = decrypt($user->google2fa_secret);
        } else {
            $google2faSecret = $google2fa->generateSecretKey();
            $request->session()->flash('google2faSecret', $google2faSecret);
        }

        $data['QRImage'] = $google2fa->getQRCodeInline(env('APP_NAME'), $user->email, $google2faSecret);
        $data['google2faSecret'] = $google2faSecret;
        return view('auth.setting', $data);
    }


}

