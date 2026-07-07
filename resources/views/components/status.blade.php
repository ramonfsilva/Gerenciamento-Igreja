@props([
    "active" => true,
    "trueLabel" => "Ativo",
    "falseLabel" => "Inativo",
])

@php
$variant = $active ? "green" : "red";
@endphp

<x-badge :variant="$variant">
    <span class="w-1.5 h-1.5 rounded-full {{ $active ? "bg-success" : "bg-danger" }} mr-1.5 inline-block"></span>
    {{ $active ? $trueLabel : $falseLabel }}
</x-badge>
