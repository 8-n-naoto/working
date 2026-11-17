<button {{ $attributes->merge(['type' => 'submit', 'class' => 'mt-3 block min-h-[46px] w-full rounded-xl bg-accent text-base font-semibold text-white']) }}>
    {{ $slot }}
</button>
