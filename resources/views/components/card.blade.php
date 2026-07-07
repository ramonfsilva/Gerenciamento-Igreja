@props([
    "padding" => true,
    "variant" => "default",
])

@php
$variants = [
    "default" => "bg-surface shadow-card border border-border",
    "elevated" => "bg-surface shadow-card-hover border border-border",
    "flat" => "bg-muted",
    "bordered" => "bg-surface border-2 border-border",
];

$hoverClass = $variant === "default" ? "hover:shadow-card-hover hover:border-primary/20 transition-all duration-normal" : "";
@endphp

<div {{ $attributes->merge(["class" => "rounded-lg {$variants[$variant]} {$hoverClass}" . ($padding ? " p-6" : "")]) }}>
    {{ $slot }}
</div>
