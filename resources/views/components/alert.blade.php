{{-- le decimos que por default es success el type --}}
@props(['type' => 'success' , 'message' => ''])

{{-- se usa cuando se quiere usar codigo de php nativo que no es de laravel --}}
@php
    $colors = [
        'success' => 'border-green-400 bg-green-100 text-green-700',
        'error' => 'border-red-700 bg-red-100 text-red-700'
    ];
    // Filtramos por el type 
    $class = $colors[$type] ?? $colors['success']

@endphp


{{-- en caso de que tengamos un mensaje --}}
@if ($message) {{-- cortamos los colores del <p> y lo movemos al @php --}}
        <p class="my-10 text-center border-l-8  py-3 text-sm fond-bold uppercase {{ $class }}">
        {{ $message}}</p>
@endif