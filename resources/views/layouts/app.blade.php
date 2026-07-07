<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}" x-data="{}" :class="{ 'dark': $store.darkMode.on }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config("app.name", "Gerenciamento de Igrejas") }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />
        @vite(["resources/css/app.css", "resources/js/app.js"])
        @livewireStyles
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('darkMode', {
                    on: Alpine.$persist(false).as('darkMode'),
                    toggle() { this.on = !this.on },
                })
            })
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-background" x-data>
            <livewire:layout.navigation />

            <div class="flex">
                <x-sidebar />

                <main class="flex-1 p-6 lg:p-8 pt-6 lg:pt-8"
                      style="padding-top: calc(1.5rem + env(safe-area-inset-top, 0px)); padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px)); padding-left: calc(1.5rem + env(safe-area-inset-left, 0px)); padding-right: calc(1.5rem + env(safe-area-inset-right, 0px));">
                    @if(session("success"))
                        <x-toast type="success" message="{{ session('success') }}" />
                    @endif

                    @if(session("error"))
                        <x-toast type="error" message="{{ session('error') }}" />
                    @endif

                    @if(session("warning"))
                        <x-toast type="warning" message="{{ session('warning') }}" />
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
