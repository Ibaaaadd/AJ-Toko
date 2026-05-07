<button {{ $attributes->merge(['type' => 'button', 'class' => 'neo-button neo-button--ghost']) }}>
    {{ $slot }}
</button>
