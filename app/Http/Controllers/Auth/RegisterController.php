<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index(){ //se usa cuando quieres ejecutar cuando das click al boton
    return view("auth.register");//automaticamente va a la carpeta resources
    }
    public function store(SignupRequest $request){ //se usa cuando envias un formulario y los datos llegan aqui 
    $data = $request->validated();

    //Almacene en la base de datos 
    $user = User::create($data);

    //se le pasa la instancia de usuario al evento y luedo ejecutara el metodo sendEmailVerificationNotification()
    event(new Registered($user));

    Auth::login($user);// para que inicie session con este usuario (LO ESTAMOS AUTENTICANDO AQUI)
    
    return redirect()->route('verification.notice');


    }
}
