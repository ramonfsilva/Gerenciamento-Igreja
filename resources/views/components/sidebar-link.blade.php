@props(["active"])

@php
$classes = ($active ?? false)
    ? "flex items-center gap-3 px-4 py-3 rounded-lg bg-primary-light text-primary font-medium transition-all duration-fast"
    : "flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-text-primary hover:bg-surface-hover transition-all duration-fast";
@endphp

<a {{ $attributes->merge(["class" => $classes]) }}>
    {{ $slot }}
</a>
