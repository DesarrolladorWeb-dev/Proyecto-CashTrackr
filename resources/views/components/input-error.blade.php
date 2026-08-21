@props(['field']) 
{{--de esta manera tenemos una variable que podremos utilizar  --}}
    @error($field)
        <p class="text-red-600">{{ $message }}</p>        
    @enderror