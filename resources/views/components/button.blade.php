@props([
    "variant" => "primary",
    "size" => "md",
    "type" => "button",
    "disabled" => false,
])

@php
$variants = [
    "primary" => "bg-primary text-on-primary hover:bg-primary-hover focus:ring-primary",
    "secondary" => "bg-surface text-text-primary border border-input hover:bg-surface-hover focus:ring-primary",
    "danger" => "bg-danger text-white hover:opacity-90 focus:ring-danger",
    "success" => "bg-success text-white hover:opacity-90 focus:ring-success",
    "warning" => "bg-warning text-white hover:opacity-90 focus:ring-warning",
    "ghost" => "text-text-secondary hover:text-text-primary hover:bg-surface-hover focus:ring-primary",
];

$sizes = [
    "sm" => "px-3 py-1.5 text-xs",
    "md" => "px-4 py-2 text-sm",
    "lg" => "px-6 py-3 text-base",
];

$base = "inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-fast focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";
@endphp

<button type="{{ $type }}" {{ $disabled ? "disabled" : "" }} {{ $attributes->merge(["class" => "$base {$sizes[$size]} {$variants[$variant]}"]) }}>
    {{ $slot }}
</button>
