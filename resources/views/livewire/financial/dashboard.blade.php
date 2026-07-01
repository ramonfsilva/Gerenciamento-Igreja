<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Financeiro</h1>
        <div class="flex flex-wrap gap-3">
            <select wire:model.live="filterMonth" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->locale("pt_BR")->translatedFormat("F") }}</option>
                @endfor
            </select>
            <select wire:model.live="filterYear" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @for($y = now()->year - 5; $y <= now()->year; $y++)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            @if(isMaster())
                <select wire:model.live="filterChurchId" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas igrejas</option>
                    @foreach($churches as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <x-card>
            <p class="text-sm font-medium text-gray-500">Entradas do Mês</p>
            <p class="text-2xl font-bold text-green-600 mt-1">R$ {{ number_format($monthIncome, 2, ",", ".") }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-medium text-gray-500">Saídas do Mês</p>
            <p class="text-2xl font-bold text-red-600 mt-1">R$ {{ number_format($monthExpense, 2, ",", ".") }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-medium text-gray-500">Saldo do Mês</p>
            <p class="text-2xl font-bold mt-1 {{ $monthBalance >= 0 ? "text-blue-600" : "text-red-600" }}">R$ {{ number_format($monthBalance, 2, ",", ".") }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-medium text-gray-500">Saldo Geral</p>
            <p class="text-2xl font-bold mt-1 {{ ($incomeTotal - $expenseTotal) >= 0 ? "text-blue-600" : "text-red-600" }}">R$ {{ number_format($incomeTotal - $expenseTotal, 2, ",", ".") }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card>
            <h2 class="text-lg font-semibold text-green-700 mb-4">Últimas Entradas</h2>
            <div class="divide-y divide-gray-100">
                @forelse($lastIncomes as $i)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $i->description ?? $i->category?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $i->date->format("d/m/Y") }} @if($i->category?->name)— {{ $i->category->name }}@endif</p>
                        </div>
                        <span class="text-sm font-semibold text-green-600 ml-3 shrink-0">R$ {{ number_format($i->amount, 2, ",", ".") }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">Nenhuma entrada no período.</p>
                @endforelse
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-red-700 mb-4">Últimas Saídas</h2>
            <div class="divide-y divide-gray-100">
                @forelse($lastExpenses as $e)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $e->description ?? $e->category?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $e->date->format("d/m/Y") }} @if($e->category?->name)— {{ $e->category->name }}@endif</p>
                        </div>
                        <span class="text-sm font-semibold text-red-600 ml-3 shrink-0">R$ {{ number_format($e->amount, 2, ",", ".") }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">Nenhuma saída no período.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
