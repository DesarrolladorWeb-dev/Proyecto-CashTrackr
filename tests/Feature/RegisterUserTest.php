

<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

//levantaran las migraciones las tablas van a existir y no vamos a tener errores 
uses(RefreshDatabase::class);


it('shows the registration screen', function(){
    // obtener la pagina register y tiene que existir.
    // el this : es la instancia que esta ejecutando esta prueba
    $response = $this->get(route('register'));

    //Queremos saber que la pagina existe 
    //para ver si el recurso existe
    $response->assertOk();
    $response->assertStatus(200);
    // ----------------------------------

    // Puedes leer tambien contenido HTML para ver si existe el texto Registrar Cuenta
    //Recuerda que debe estar igual
    $response->assertSee('Crear Cuenta');
    $response->assertSee('Registrarme');

    //Si estan en orden de arriba hacia abajo
    $response->assertSeeInOrder([
        'Crear Cuenta',
        'Registrarme'
    ]);


    // dd($response); //el contenido solo aparecera en la consola  - es todo el contenido de la pagina 
});

it('registers a new user as univerified and dispatches the registered event', function(){

    //Si recuerdas cuando un usuario es registrdo se envia un evento que envia un email 
    //y si envias cientos de email en la parte de testing facilmente se te acabaran el plan gratis facilmente
    //entonces podemos colocar :
    Event::fake(); //esto lo que hace es simular el evento y lo usamos abajo


    //en este caso enviaremos los datos  hacia la ruta "register.store" para registrar un usuario.
    $response = $this->post(route('register.store'),[
        'name' => 'Juan Perez',
        'email' => 'juan@juan.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);

    //y espera que hay una redireccion hacia la ruta 'verification.notice
    //Porque en el RegisterController.php lo lleva hacia esa ruta - en el metodo store.
    $response->assertRedirect(route('verification.notice'));

    // nos aseguramos de que exista en la base de datos 
    $user = User::where('email','juan@juan.com')->first();

    // pruebas para el $user
    //Esperamos que no sea nulo
    expect($user)->not->toBeNull();

    //tambien podremos revisar los valores de la base de datos 
    // name: no tiene parentesis porque es una propiedad
    expect($user->name)->toBe('Juan Perez');
    expect($user->email)->toBe('juan@juan.com');

    //recuerda cuando ingresas un registro los usuarios no estan verificados 
    //puedes usar un metodo de laravel en pest
    // "Esperamos que el email de verificacion aun este como false"
    expect($user->hasVerifiedEmail())->toBeFalse();


    //si recuerdas que el evento es de Register y escribes Register
    Event::assertDispatched(Registered::class);

});

it('should validate required fields when the request body is empty',function(){
    //enviamos el arreglo vacio
    $response = $this->post(route('register.store'),[]);


    //y revisar que existan errores en . name , email ...
    $response->assertSessionHasErrors([
        'name',
        'email',
        'password'
    ]);
    //tambien puedes validar por el texto 

     $response->assertSessionHasErrors([
        'name' => 'El Nombre es obligatorio',
        'email' => 'El Email es obligatorio',
        'password' => 'La Contraseña es obligatoria'
    ]);
});

it('prevents duplicate email addresses',function(){

    //no puedes crear el otro usuario para compararlo con ese usuario 
    //entonces usaremos los factories : que ingresara datos de prueba 
    User::factory()->create([ //se agrego los corchetes para que agregemos algo especifico 
        'email' => 'juan@juan.com'
    ]);
    //despues ingresa este usuario con ese email 
    $response = $this->post(route('register.store'),[
        'name' => 'Juan Perez',
        'email' => 'juan@juan.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);

    $response->assertRedirect(); //esperamos un redirect 

    $response->assertSessionHasErrors([ //y tambien esperamos este mensaje en pantalla
        'email' => 'Este correo ya esta registrado',
    ]);

});
it('sends the verification email notification after registration',function(){


    //porque si recuerdas el envio de emails es una notificacion - porque :
    //Motifications/VerifyEmail.php - metodo via() - vemos que retorna un ['mail']
    // las notificaciones son en dos partes : via email o en la base de datos , en este caso es via email
    //entonces usamo Notification:: para que simule la notificacion - nota: no es un evento es una notificacion
    Notification::fake(); //use Illuminate\Support\Facades\Notification;



    $response = $this->post(route('register.store'),[
        'name' => 'Juan Perez',
        'email' => 'juan@juan.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    //obtenemos el usuario de la base de datos 
    $user = User::where('email','juan@juan.com')->first();


    //Notificamos a ese usuario:
    //vemos que no envio el email real ,si no que utiliza ese 
    //fake() para simular pero si se envio a ejecutar esa clase (VerifyEmail) 
    //significa que se ejecuto correctamente 
    Notification::assertSentTo($user, VerifyEmail::class);
});
it('verifies the user email from a signed verification link', function(){

    //Crear un usuario que no este verificado para verificarlo abajo
    $user = User::factory()->unverified()->create();

    //usaremo del VerifyEmail.php
    //URL: Illuminate\Support\Facades\URL;
    //Esto es como si Hubiera enviado al CORREO del Usuario para que verifique su cuenta - Aqui solo se crea la url de verificacion
    $verificationUrl = URL::temporarySignedRoute( //creamos el Url temporal
    'verification.verify',
    now()->addMinutes(60),
            [//Y LE PASAMOS :
                'id' => $user->id, //como no tiene getKey() el user lo cambiamos por id
                //el email del usuario hasheado
                'hash' => sha1($user->email), //getEmailForVerification() - esto es para la verificacion , pero nosotros no estamos creando un usuario que no este verificado ,entonces solo usamos email
            ]);

            //cuando el usuario se registra le llega ese email , entonces tendria que ir a emailtrap y despues volver a nuestra aplicacion 
            //es complicado de testear , porque se ejecutara en el cli y no hay ningun navegador, Pest tiene funciones para que un usuario simule ciertas acciones 
            //actingAs : vas a estar actuando como $user - entonces -> visita la URL (El cliente entra a su correo y luego da click en la URL(para verificar su cuenta) para ir directo al dashboard)
            $response = $this->actingAs($user)->get($verificationUrl);

            //cuando el usuario confirma su cuenta es redirigido hacia el dashboard 
            $response->assertRedirect(route('dashboard'));


            // dd($user) //puedes ver como change(hubo un cambio) en email_verified_at

            //luego verificar que el campo email_verified_at este rellenado
            
            //esperamos que el usuario esta verificado - porque a esas alturas el usuario ya se verifico
            expect($user->hasVerifiedEmail())->toBeTrue();
});
it('does not allow an unverified user to access the dashboard', function(){
        $user = User::factory()->unverified()->create();
        //vamos a ir directamente a la ruta del dashboard sin verificar la cuenta como hice anteriormente(sin ese paso)
        $response = $this->actingAs($user)->get(route('dashboard'));
        //le llevara hacia esta pagina
        $response->assertRedirect(route('verification.notice'));
});

it('allows a verfied user to access the dashboard', function(){
    //en este caso es un usuario verificado , quitamos unverified
        $user = User::factory()->create([
            'email_verified_at' => now() //esta verificado con esa fecha
        ]);
        //vamos a ir directamente a la ruta del dashboard sin verificar la cuenta como hice anteriormente(sin ese paso)
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
});