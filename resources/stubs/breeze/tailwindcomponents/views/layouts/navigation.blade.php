<div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false" class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
     class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300 transform bg-blue-500 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0">
     <div class="flex items-center justify-center mt-4">
        <div class="p-2 max-w-sm w-full">
            <div class="flex justify-center bg-white  rounded p-0">
                <a href="#" class="flex justify-between">
                    <img src="{{ asset('images/logo/kyocera-avx-logo.png') }}" alt="kyocera-avx-logo"
                        class="w-15 h-15 mr-2 py-4">
                </a>
            </div>
        </div>
    </div>
    <nav class="mt-10" x-data="{ isMultiLevelMenuOpen: false }">
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
            </x-slot>
            {{ __('Dashboard') }}
        </x-nav-link>

        <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </x-slot>
            {{ __('Users') }}
        </x-nav-link>

        <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
            <x-slot name="icon">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
            </x-slot>
            {{ __('About us') }}
        </x-nav-link>

        <x-nav-link href="#" @click="isMultiLevelMenuOpen = !isMultiLevelMenuOpen">
            <x-slot name="icon">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
            </x-slot>
            Two-level menu
        </x-nav-link>
        <template x-if="isMultiLevelMenuOpen">
            <ul x-transition:enter="transition-all ease-in-out duration-300"
                x-transition:enter-start="opacity-25 max-h-0" x-transition:enter-end="opacity-100 max-h-xl"
                x-transition:leave="transition-all ease-in-out duration-300"
                x-transition:leave-start="opacity-100 max-h-xl" x-transition:leave-end="opacity-0 max-h-0"
                class="p-2 mx-4 mt-2 space-y-2 overflow-hidden text-sm font-medium text-white bg-gray-700 bg-opacity-50 rounded-md shadow-inner"
                aria-label="submenu">
                <li class="px-2 py-1 transition-colors duration-150">
                    <a class="w-full" href="#">Child menu</a>
                </li>
            </ul>
        </template>
    </nav>
</div>
