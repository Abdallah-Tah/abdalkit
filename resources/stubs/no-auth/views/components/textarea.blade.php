{{-- textarea.blade.php --}}
<textarea name="{{ $name }}" id="{{ $id }}" class="block mt-1 w-full rounded-md form-input border-gray-300 shadow-sm"
    placeholder="{{ $placeholder }}" rows="{{ $rows }}" {{ $attributes }}>{{ $slot }}</textarea>
