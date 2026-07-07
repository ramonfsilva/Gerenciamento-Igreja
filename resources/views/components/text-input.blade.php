@props(["disabled" => false])

<input @disabled($disabled) {{ $attributes->merge(["class" => "border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary placeholder:text-text-muted transition-all duration-fast"]) }}>
