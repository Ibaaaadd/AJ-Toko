@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'neo-alert']) }}>
        {{ $status }}
    </div>
@endif
