<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function index()
    {
       
        return view('register');
    }

    public function store(Request $request)
    {
        $credentials = request()->validate([
            'name'      => ['required', 'min:3'],
            'email'     => ['required', 'email', 'max:64', 'unique:users,email'],
            'password'  => ['required', 'min:6', 'max:20']
        ]);
        
        
        $user = User::create([
          //  'user_id'   => (string) Str::uuid(),
            'name'      => $credentials['name'],
            'email'     => $credentials['email'],
            'password'  => $credentials['password']
        ]);

        Roles::create([
            'user_id' => $user->user_id,
            'role' => 'user'
        ]);
    

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/home');
        }
 
        $unkEr = 'Unknown error';
        return redirect("/register")->withErrors([
            'email' => $unkEr,
            'name' => $unkEr,
            'password'  => $unkEr
        ])->withInput($request->input());

    }
}
