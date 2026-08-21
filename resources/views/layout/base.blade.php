<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'CashTrackr') }} - @yield('title')</title>
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    
        @endif
    </head>
    <body>
            <header class="bg-purple-950 py-5">
                <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center 
                lg:justify-between">
                    <div class="w-full max-w-100">
                        {{-- asset : apunta a la carpeta public --}}
                            <img src="{{ asset('img/logo.svg') }}" alt="cashtrakr logo" class="w-full blocks">
                    </div>
                    {{-- si no existe la ruta en web.php no lo muestra --}}
                    <nav class="flex flex-col lg:flex-row items-center gap-4">

                        @auth
                            <p class="text-white text-xl">Hola {{ auth()->user()->name }}</p>
                        @else
                            

            @if (Route::has('login'))
                        <a
                        class="text-white font-bold uppercase p-2"
                        href="{{ route('login') }}">Iniciar Sesion</a>
                        <a
                        class=" font-bold uppercase border-2 border-amber-500 px-5 py-2 text-amber-500"
                        href="{{ route('register') }}">Crear Cuenta</a>
                    @endif
                        @endauth

                    </nav>
                </div>

            </header>
        {{-- este yield  le da un nombre de un luegar reservado y con el @section en el otro archivo, se posicionara en ese lugar--}}
        @yield('contents')

    </body>

</html>