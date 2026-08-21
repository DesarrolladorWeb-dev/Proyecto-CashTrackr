<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/register', [RegisterController::class,'index'])->name('register'); //para usar este nombre clave como la ruta : usando route()
Route::post('/auth/register', [RegisterController::class,'store'])->name('register.store'); 


Route::get('/auth/login', [LoginController::class,'index'])->name('login');
Route::post('/auth/login', [LoginController::class,'store'])->name('login.store'); 



// ver el VerifyEmail.php vemos que le estamos pasando el id y el hash , el cliente da click en la url temporal y lo envia aqui 
// /email/verify/id/hash           -> hash no es igual a encriptado  
Route::get('/email/verify/{id}/{hash}',function(EmailVerificationRequest $request){
    $request->fulfill(); //tomara los valores de la url y va a intentar confirmar la cuenta 
    return redirect()->route('dashboard')->with('success','Tu correo fue verificado Correctamente.
    Ya puedes Crear Presupuestos y Gastos.');
    //auth : middleware para verificar la cookie de sesion al momento de autenticarlo en el web.php
    // signed : verificar que no haya sido modificada el hash
})->middleware(['auth','signed'])->name('verification.verify'); //cuando hace click en el boton de Confirmar Cuenta lo envia aqui


Route::get('/email/verify', function(){
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


Route::post('/email/verification-notification',function(Request $request){
//  dd($request->user());
    $request->user()->sendEmailVerificationNotification(); //envia el correo
    return back()->with('success','Se ha reenviado el correo de verificacion');
})->middleware(['auth','throttle:1,1'])->name('verification.send');



// verified : que el usuario tenga que haber verificcado su cuenta - el campo del db "email_verified_at".
Route::get('/dashboard', function(){
    return view('dashboard');
})->middleware(['auth','verified'])->name('dashboard');