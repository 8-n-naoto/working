@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full min-h-[46px] rounded-[10px] border border-line bg-card px-3 text-base text-ink focus:border-accent focus:ring-accent']) }}>
