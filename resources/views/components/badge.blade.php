@props([
    "variant" => "gray",
    "size" => "sm",
])

@php
$variants = [
    "gray" => "bg-muted text-text-secondary",
    "blue" => "bg-primary-light text-primary",
    "green" => "bg-success-light text-success",
    "red" => "bg-danger-light text-danger",
    "yellow" => "bg-warning-light text-warning",
    "indigo" => "bg-indigo-100 text-indigo-800",
];

$sizes = [
    "sm" => "px-2 py-0.5 text-xs",
    "md" => "px-3 py-1 text-sm",
];
@endphp

<span {{ $attributes->merge(["class" => "inline-flex items-center font-medium rounded-full {$sizes[$size]} {$variants[$variant]}"]) }}>
    {{ $slot }}
</span>
