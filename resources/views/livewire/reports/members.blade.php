<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-text-primary">Relatório de Membros</h1>
        @if(isMaster())
            <select wire:model.live="filterChurchId" class="px-4 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todas igrejas</option>
                @foreach($churches as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <x-card>
            <p class="text-sm font-medium text-text-secondary">Total de Membros</p>
            <p class="text-3xl font-bold text-text-primary mt-1">{{ $total }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-medium text-text-secondary">Membros Ativos</p>
            <p class="text-3xl font-bold text-success mt-1">{{ $active }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-medium text-text-secondary">Inativos</p>
            <p class="text-3xl font-bold text-danger mt-1">{{ $total - $active }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">Membros por Situação</h2>
            <div class="space-y-3">
                @foreach(["Ativo" => "green", "Congregado" => "blue", "Visitante" => "yellow", "Afastado" => "red", "Transferido" => "gray"] as $status => $color)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500"></span>
                            <span class="text-sm text-text-primary">{{ $status }}</span>
                        </div>
                        <span class="font-semibold {{ $byStatus[$status] > 0 ? "text-text-primary" : "text-text-muted" }}">{{ $byStatus[$status] }}</span>
                    </div>
                @endforeach
                <div class="pt-3 border-t border-border flex items-center justify-between font-semibold text-text-primary">
                    <span>Total</span>
                    <span>{{ $total }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">🎂 Aniversariantes do Mês</h2>
            @if($birthdays->count())
                <div class="divide-y divide-border">
                    @foreach($birthdays as $member)
                        <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                            <div>
                                <p class="text-sm font-medium text-text-primary">{{ $member->name }}</p>
                                <p class="text-xs text-text-secondary">{{ $member->birth_date->format("d/m") }} — {{ $member->birth_date->age }} anos</p>
                            </div>
                            <x-badge variant="green">{{ $member->birth_date->format("d/m") }}</x-badge>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-text-secondary py-4 text-center">Nenhum aniversariante este mês.</p>
            @endif
        </x-card>
    </div>
</div>
