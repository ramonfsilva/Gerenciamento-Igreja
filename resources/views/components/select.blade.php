@props(["disabled" => false, "options" => []])

<select @disabled($disabled) {{ $attributes->merge(["class" => "border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary transition-all duration-fast"]) }}>
    {{ $slot }}
</select>
