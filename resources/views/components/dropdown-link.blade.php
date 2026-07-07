@props(['href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm text-text-secondary hover:bg-muted hover:text-text-primary transition-colors duration-fast']) }}>
    {{ $slot }}
</a>
