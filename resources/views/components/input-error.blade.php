@props(['messages' => null])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-danger space-y-1 mt-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
