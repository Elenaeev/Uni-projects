<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\User;
use Illuminate\Validation\Rules;

class SignInController extends Controller
{
    public function index()
    {
        return view('signin');
    }

    public function signin(Request $request)
    {
        $credentials = request()->validate([
            'email'     => ['required', 'email', 'max:64'],
            'password'  => ['required', 'min:6', 'max:20']
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/home');
        }
        
        $user = User::where('email', $credentials['email'])->first();

        $errors = ['email'=>'', 'password'=>''];
        if ($user)
            $errors['password'] = 'Wrong passowrd';
        else
            $errors['email'] = 'Email does not exist';
        
        return redirect("/signin")->withErrors($errors)->withInput($request->input());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect("/home");
    }
}
