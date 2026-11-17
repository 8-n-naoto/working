@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 mt-3 block text-[13px] text-sub']) }}>
    {{ $value ?? $slot }}
</label>
