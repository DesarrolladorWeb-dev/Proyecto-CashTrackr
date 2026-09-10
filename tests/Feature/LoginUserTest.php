<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the login screen',function(){
    $response = $this->get(route('login'));
    //que existe la pagina y que no da niun error 
    $response->assertOk();
});

it('logs in a verified user successfully', function() {
    //los demas datos lo inventara factory pero respetara lo que le estoy pasando
    User::factory()->create([
        //para saber que tiene el email
        'email'  => 'juan@juan.com',
        'password' => bcrypt('password'),
        // porque el usuario debe estar verificado
        'email_verified_at' => now()
    ]);

    // dd($user);
    //realizamos la peticion de tipo post al login.store
    $response = $this->post(route('login.store'),[
        'email'  => 'juan@juan.com',
        'password' => 'password'
    ]);

    //esperamos un redirect hacia la ruta de un dashboard
    $response->assertRedirect(route('dashboard'));
    //Y tambien el usuario debe estar autenticado - El usuario estara autenticado 
    $this->assertAuthenticated();
});

it('does not log in with invalid credentials', function() {
 
    User::factory()->create([
        'email'  => 'juan@juan.com',
        'password' => bcrypt('password')
    ]);

    //usamos el from, y aqui dice "desde esta pagina "login" - enviamos el post -> hacia esta otra pagina "login.store"
    $response = $this->from(route('login'))->post(route('login.store'),[
        'email'  => 'juan@juan.com',
        'password' => 'incorrect-password'
    ]);
    // NOTA : Arriba aqui ("logs in a verified user successfully") lo enviamos un post directamente porque no estamos leyendo la respuesta.
            //pero aqui le decimos de donde lo enviamos "$response = $this->from(route('login')) "
            //y despues recuperamos los mensajes en esa misma pagina "$response->assertRedirect(route('login'));"
    //como recuerdas - regresara otra vez hacia login si hay un error con un mensaje.
    $response->assertRedirect(route('login'));
    //y el error se guarda en un session por eso colocamos
    $response->assertSessionHas('error','Credenciales incorrectas');
    //Aqui decimos que el usuario tiene que estar como invitado
    $this->assertGuest();
});

it('prevent unverified user from accessing dashboard',function(){

    User::factory()->unverified()->create([
        
        'email'  => 'juan@juan.com',
        'password' => bcrypt('password')
    ]);

    
    
    $response = $this->post(route('login.store'),[
        'email'  => 'juan@juan.com',
        'password' => 'password'
    ]);
    //un usuario no verificado su cuanta 
    //No puede acceder al dashboard
    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    
    //Y es llevado  hacia "verification.notice"
    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertRedirect(route('verification.notice'));
});

it('does not allow access to dashboard if email is not verified', function(){
    $user = User::factory()->create([
        //este campo estara como null
        'email_verified_at' => null
    ]);
    //ni un usuario  verificado puede ingresar al dashboard
    //si quiero entrar directamente al dashboard no me dejara  
    $response = $this->actingAs($user)->get(route('dashboard'));
    // y me redirigira a:
    $response->assertRedirect(route('verification.notice'));
});

it('allow access to dashboard if email is verified', function(){
    $user = User::factory()->create([
        
        'email_verified_at' => now() //verificado
    ]);

    //el usuario intenta acceder al dashboard
    $response = $this->actingAs($user)->get(route('dashboard'));
  
    $response->assertOk();
});
it('fails login if user does not exist',function(){
    //como sabes es si haces un login desde la pagina de login 
    // y si no eres te redirige hacia la misma pagina con mensajes de que "que no encontramos una cuenta con ese correo electronico"
    // por eso se usa el from porque nos regresa hacia la misma pantalla
    $response = $this->from(route('login'))
                    ->post(route('login.store'),[
                        'email' => 'noexiste@dominio.com',
                        'password' => 'password'
                    ]);

    //esperamos un redirect hacia la ruta de login 
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'No encontramos una cuenta con ese correo electronico'
    ]);
    $this->assertGuest();
});