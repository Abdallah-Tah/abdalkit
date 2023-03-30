<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="shortcut icon" href="{{ asset('images/logo/favicon.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">



    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-200 font-roboto">
        @include('layouts.navigation')

        <div class="flex overflow-hidden flex-col flex-1">
            @include('layouts.header')

            <main class="overflow-y-auto overflow-x-hidden flex-1 bg-slate-200">
                <div class="container px-6 py-8 mx-auto">
                    @if (isset($header))
                        <h3 class="mb-4 text-3xl font-medium text-gray-700">
                            {{ $header }}
                        </h3>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        window.addEventListener('swal:modal', event => {
            swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.type,
                html: event.detail.html,
                //iconColor: "#CC0000",
                // confirmButtonColor: "#CC0000",  
            });
        });
        window.addEventListener('swal:confirm', event => {
            swal.fire({
                    title: event.detail.title,
                    text: event.detail.text,
                    icon: event.detail.type,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('delete', event.detail.id);
                    }
                });
        });
    </script>
</body>

</html>
