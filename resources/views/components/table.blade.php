@props([
    "headers" => [],
    "striped" => false,
    "hover" => true,
    "empty" => "Nenhum registro encontrado.",
    "cols" => null,
])

<div {{ $attributes->merge(["class" => "bg-surface rounded-lg shadow-card border border-border overflow-hidden"]) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border">
            @if(count($headers) > 0)
                <thead class="bg-muted">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="bg-surface divide-y divide-border">
                @if ($slot->isEmpty())
                    <tr>
                        <td colspan="{{ $cols ?? count($headers) }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <x-icon name="info" size="8" class="text-text-muted" />
                                <p class="text-sm text-text-muted">{{ $empty }}</p>
                            </div>
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>
</div>
