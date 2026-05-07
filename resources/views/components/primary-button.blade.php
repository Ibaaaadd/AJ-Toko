<button {{ $attributes->merge(['type' => 'submit', 'class' => 'neo-button neo-button--primary']) }}>
    {{ $slot }}
</button>
