<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Relatórios Financeiros</h1>
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
        <x-card>
            <p class="text-sm text-gray-500">Total Entradas</p>
            <p class="text-xl font-bold text-green-600">R$ {{ number_format($totals["income"], 2, ",", ".") }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Total Saídas</p>
            <p class="text-xl font-bold text-red-600">R$ {{ number_format($totals["expense"], 2, ",", ".") }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Saldo</p>
            <p class="text-xl font-bold {{ $totals["balance"] >= 0 ? "text-blue-600" : "text-red-600" }}">R$ {{ number_format($totals["balance"], 2, ",", ".") }}</p>
        </x-card>
    </div>

    <x-table :headers="['Categoria', 'Entradas', 'Saídas', 'Saldo']">
        @forelse($reportRows as $row)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    {{ $row["category"] }}
                    <x-badge variant="{{ $row['type'] === 'income' ? 'green' : 'red' }}" class="ml-2">
                        {{ $row["type"] === "income" ? "Entrada" : "Saída" }}
                    </x-badge>
                </td>
                <td class="px-6 py-4 text-sm text-right {{ $row["income"] > 0 ? "text-green-600 font-medium" : "text-gray-400" }}">
                    R$ {{ number_format($row["income"], 2, ",", ".") }}
                </td>
                <td class="px-6 py-4 text-sm text-right {{ $row["expense"] > 0 ? "text-red-600 font-medium" : "text-gray-400" }}">
                    R$ {{ number_format($row["expense"], 2, ",", ".") }}
                </td>
                <td class="px-6 py-4 text-sm text-right font-medium {{ $row["balance"] >= 0 ? "text-blue-600" : "text-red-600" }}">
                    R$ {{ number_format($row["balance"], 2, ",", ".") }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Nenhuma movimentação no período.</td>
            </tr>
        @endforelse
        <tr class="bg-gray-50 font-semibold">
            <td class="px-6 py-3 text-sm text-gray-900">Total</td>
            <td class="px-6 py-3 text-sm text-right text-green-600">R$ {{ number_format($totals["income"], 2, ",", ".") }}</td>
            <td class="px-6 py-3 text-sm text-right text-red-600">R$ {{ number_format($totals["expense"], 2, ",", ".") }}</td>
            <td class="px-6 py-3 text-sm text-right {{ $totals["balance"] >= 0 ? "text-blue-600" : "text-red-600" }}">R$ {{ number_format($totals["balance"], 2, ",", ".") }}</td>
        </tr>
    </x-table>
</div>
