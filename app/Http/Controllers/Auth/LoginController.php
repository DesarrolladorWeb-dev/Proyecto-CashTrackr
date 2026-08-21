<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){ 
    return view("auth.login"); //automaticamente va a la carpeta resources
    }

    public function store(SignInRequest $request){

            $data = $request->validated();

            if(!Auth::attempt($data)){
                //en caso de que sean incorrectos 
                return back()->with('error','Creadenciales incorrectas');
            }

            //Hasta aqui significa que ya se valido correctamente 
            return redirect()->route('dashboard');
    }

}
