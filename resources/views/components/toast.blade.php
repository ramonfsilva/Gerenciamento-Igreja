@props([
    "type" => "success",
    "message" => "",
])

@php
$styles = [
    "success" => "bg-success-light border-success text-success",
    "error" => "bg-danger-light border-danger text-danger",
    "warning" => "bg-warning-light border-warning text-warning",
    "info" => "bg-primary-light border-primary text-primary",
];

$icons = [
    "success" => "check",
    "error" => "close",
    "warning" => "warning",
    "info" => "info",
];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out-cubic duration-slow"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-fast"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="mb-4 px-4 py-3 rounded-lg border {{ $styles[$type] }}">
    <div class="flex items-center gap-2">
        <x-icon :name="$icons[$type]" size="5" />
        <p class="text-sm font-medium">{{ $message }}</p>
        <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 transition-opacity">
            <x-icon name="close" size="4" />
        </button>
    </div>
</div>
