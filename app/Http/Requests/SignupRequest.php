<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest
{
   //el usuario esta autorizado a ejecutar este request
    //por default esta false, para que lo pueda utilizar lo cambiamos a true
    //si deseas puedes eliminar authorize() porque por defecto es true
  public function authorize(): bool
    {
        return true;
    }

    public function messages():array{
        //cuando ocurre ese error mostrara
        return [
            'name.required' => 'El Nombre es obligatorio',
            'email.required' => 'El Email es obligatorio',
            'email.email' => 'E-mail no valido',
            'email.unique' => 'Este correo ya esta registrado',
            'password.required' => 'La Contraseña es obligatoria',
            'password.confirmed' => 'Las Contraseñas no coinciden',
            // :min = aqui tomara el numero que le colocamos en las reglas
            'password.min' => 'la Contraseña debe tener al menos :min caracteres',
            'password.letters'=> 'El Password debe contener al menos una letra',
            'password.mixed'=> 'La Contraseña debe tener al menos 1 letra Mayuscula y letra minuscula',
            'password.symbols' => 'La Contraseña debe tener al menos 1 caracter especial (^@_-)',
            'password.numbers' =>'La Contraseña debe tener al menos 1 numero',
            'password.uncompromised' => 'La Contraseña ha aparecido en filtraciones de datos. Elige una mas segura'
        ];
    }



    /**
     * Aqui estan las reglas de validacion
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // confirmed -> debe ser igual al campo "repetir password "
        return [
            'name' => ['required', 'string'],
            'email' => ['required','email','unique:users,email'],//users = tabla , email=columna //revisaran que sean unicas y no enviara error
            'password' => ['required', 'confirmed',Password::min(8)
            //letters() -> el password requiere almenos una letra
            // ->mixedCase() //almenos una mayuscula y una minuscula
            // ->symbols() //almenos un simbolo (caracter especial)
            // ->numbers() //que almenos tenga un numero
            // ->uncompromised() //revisa la contraseña ingresada que no se encuentre en un diccionario de contraseñas
            ]
        ];
    }
}



