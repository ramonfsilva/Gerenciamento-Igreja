@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-start text-base font-medium text-primary bg-primary-light focus:outline-none focus:text-primary focus:bg-primary-light focus:border-primary-hover transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-text-secondary hover:text-text-primary hover:bg-muted hover:border-border focus:outline-none focus:text-text-primary focus:bg-muted focus:border-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
