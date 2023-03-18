@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block mt-1 w-full rounded-md form-input border-gray-300 shadow-sm']) !!}>
