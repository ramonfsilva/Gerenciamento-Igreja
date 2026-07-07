<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-text-primary">Dashboard</h1>
    </div>

    @if(isMaster())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Total de Igrejas</p>
                        <p class="text-3xl font-bold text-text-primary mt-1">{{ $totalChurches }}</p>
                    </div>
                    <div class="p-3 bg-primary-light rounded-full">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Total de Membros</p>
                        <p class="text-3xl font-bold text-text-primary mt-1">{{ $totalMembers }}</p>
                    </div>
                    <div class="p-3 bg-primary-light rounded-full">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Igrejas Ativas</p>
                        <p class="text-3xl font-bold text-text-primary mt-1">{{ $activeChurches }}</p>
                    </div>
                    <div class="p-3 bg-success-light rounded-full">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </x-card>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Igreja</p>
                        <p class="text-2xl font-bold text-text-primary mt-1 leading-tight">{{ $church?->name ?? "Sem igreja" }}</p>
                    </div>
                    <div class="p-3 bg-primary-light rounded-full shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Membros Ativos</p>
                        <p class="text-3xl font-bold text-text-primary mt-1">{{ $activeMembers }}</p>
                    </div>
                    <div class="p-3 bg-success-light rounded-full">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-text-secondary">Visitantes</p>
                        <p class="text-3xl font-bold text-text-primary mt-1">{{ $visitors }}</p>
                    </div>
                    <div class="p-3 bg-warning-light rounded-full">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
            </x-card>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @can("member.view")
            <a href="{{ route("members.index") }}" wire:navigate class="block">
                <x-card>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-primary-light rounded-full">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-text-primary">Membros</p>
                            <p class="text-sm text-text-secondary">Gerenciar cadastro</p>
                        </div>
                    </div>
                </x-card>
            </a>
        @endcan

        @can("financial.view")
            <a href="{{ route("financial.dashboard") }}" wire:navigate class="block">
                <x-card>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-success-light rounded-full">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-text-primary">Financeiro</p>
                            <p class="text-sm text-text-secondary">Entradas e saídas</p>
                        </div>
                    </div>
                </x-card>
            </a>
        @endcan

        @can("church.view")
            <a href="{{ route("churches.index") }}" wire:navigate class="block">
                <x-card>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-primary-light rounded-full">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-text-primary">Igrejas</p>
                            <p class="text-sm text-text-secondary">Gerenciar igrejas</p>
                        </div>
                    </div>
                </x-card>
            </a>
        @endcan
    </div>

    @if(!isMaster() && $birthdayMembers->isNotEmpty())
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">🎂 Aniversariantes do Mês</h2>
            <div class="divide-y divide-border">
                @foreach($birthdayMembers as $member)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm font-medium text-text-primary">{{ $member->name }}</p>
                            <p class="text-xs text-text-secondary">{{ $member->birth_date->format("d/m") }} — {{ $member->birth_date->age }} anos</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif
</div>
