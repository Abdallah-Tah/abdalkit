<x-guest-layout>
    <a href="/" class="flex justify-center items-center">
        <x-application-logo class="w-20 h-20 text-gray-500 fill-current"/>
    </a>

    <form method="POST" action="{{ route('password.set', ['user' => $userId]) }}">
    @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{-- {{ $request->route('token') }} --}}">

        <!-- Password -->
        <div class="mt-3">
            <x-input-label for="password" :value="__('Password')"/>
            <x-text-input type="password"
                     name="password"
                     id="password"
                     required autocomplete="current-password"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-3">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>
            <x-text-input type="password"
                     name="password_confirmation"
                     id="password_confirmation"
                     required/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full">
                {{ __('Set Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
