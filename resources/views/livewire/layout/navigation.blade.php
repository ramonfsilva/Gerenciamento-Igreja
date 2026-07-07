<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-nav-bg border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" wire:navigate class="text-xl font-heading font-bold text-text-primary">
                    {{ config('app.name', 'Gerenciamento de Igrejas') }}
                </a>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <button @click="$store.darkMode.toggle()"
                            class="p-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-surface-hover transition-all duration-fast"
                            x-tooltip="$store.darkMode.on ? 'Modo claro' : 'Modo escuro'">
                        <template x-if="$store.darkMode.on">
                            <x-icon name="sun" size="5" />
                        </template>
                        <template x-if="!$store.darkMode.on">
                            <x-icon name="moon" size="5" />
                        </template>
                    </button>

                    <div class="hidden sm:flex sm:items-center sm:gap-2 mr-2">
                        <span class="text-sm text-text-secondary">{{ auth()->user()->name }}</span>
                        @if(currentChurch())
                            <span class="text-sm text-text-muted">|</span>
                            <span class="text-sm text-text-muted">{{ currentChurch()->name }}</span>
                        @endif
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-text-secondary bg-nav-bg hover:text-text-primary focus:outline-none transition duration-fast">
                                <div>{{ auth()->user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile') }}" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>
        </div>
    </div>
</nav>
