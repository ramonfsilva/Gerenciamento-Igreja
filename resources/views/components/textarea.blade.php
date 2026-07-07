@props(["disabled" => false, "rows" => 3])

<textarea @disabled($disabled) {{ $attributes->merge(["class" => "border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary placeholder:text-text-muted transition-all duration-fast"]) }} rows="{{ $rows }}">{{ $slot }}</textarea>
