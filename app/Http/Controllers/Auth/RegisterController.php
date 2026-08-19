<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(){ //se usa cuando quieres ejecutar cuando das click al boton
    return view("auth.register");//automaticamente va a la carpeta resources
    }
    public function store(SignupRequest $request){ //se usa cuando envias un formulario y los datos llegan aqui 
    $data = $request->validated();

    //Almacene en la base de datos 
    User::create($data);

    
    }
}
