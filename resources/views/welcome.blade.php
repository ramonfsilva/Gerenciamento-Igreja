<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <title>{{ config('app.name', 'Gerenciamento de Igrejas') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="relative min-h-screen flex flex-col items-center justify-center bg-background">
            <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-primary-light to-transparent opacity-50 pointer-events-none"></div>

            <div class="relative w-full max-w-2xl px-6 lg:max-w-4xl">
                <header class="flex items-center justify-between py-10">
                    <a href="/" class="text-xl font-heading font-bold text-primary">
                        {{ config('app.name', 'Gerenciamento de Igrejas') }}
                    </a>
                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </header>

                <main class="mt-16 text-center">
                    <x-application-logo class="w-24 h-24 mx-auto mb-8" style="color: var(--color-primary)" />

                    <h1 class="text-4xl lg:text-5xl font-heading font-bold text-text-primary mb-4">
                        Gerencie sua igreja com excelência
                    </h1>
                    <p class="text-lg text-text-secondary max-w-xl mx-auto mb-10">
                        Cadastro de membros, controle financeiro, relatórios e muito mais — tudo em um só lugar.
                    </p>

                    <div class="flex items-center justify-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" wire:navigate
                                   class="inline-flex items-center px-6 py-3 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-hover transition-all duration-fast shadow-card hover:shadow-card-hover">
                                    Acessar Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" wire:navigate
                                   class="inline-flex items-center px-6 py-3 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-hover transition-all duration-fast shadow-card hover:shadow-card-hover">
                                    Entrar
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" wire:navigate
                                       class="inline-flex items-center px-6 py-3 border border-input text-text-secondary rounded-lg font-medium hover:bg-surface-hover hover:text-text-primary transition-all duration-fast">
                                        Cadastrar
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </main>

                <footer class="mt-24 pb-8 text-center text-sm text-text-muted">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Gerenciamento de Igrejas') }}. Todos os direitos reservados.
                </footer>
            </div>
        </div>
    </body>
</html>
