@extends('layout.auth') 

@section('title')
    Confirma tu Cuenta
@endsection

@section('auth-contents')
<p class="mt-5 text-lg">Tu cuenta fue creada con exito, Ahora solo debes confirmala , revisa tu e-mail</p>

@if (session('success'))
        <x-alert :message="session('success')"/>
@endif


<form method="POST" action="{{ route('verification.send') }}">
    <input 
        type="text"
        class="bg-amber-500 w-full text-center mt-5 px-5 py-2 uppercase font-bold cursor-pointer"
        value="Reenviar correo de verificacion"
    >

</form>
@endsection
