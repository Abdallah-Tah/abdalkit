@props(['value'])

<label class="inline-flex items-center">
    <input type="checkbox" {{ $attributes->merge(['class' => 'rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50']) }} value="{{ $value }}">
    <span class="ml-2 text-sm">{{ $slot }}</span>
</label>
