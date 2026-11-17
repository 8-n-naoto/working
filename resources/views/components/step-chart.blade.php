@props(['days', 'goal' => 8000])
@php
    // データなし日は 0。最大値 0 でも 0 除算しない（max は最低 1）
    $values = array_map(fn ($d) => max(0, (int) $d->steps), $days);
    $count = max(1, count($days));
    $max = max(1, (int) $goal, ...($values ?: [0]));
    $colW = 280 / $count;
    $barW = $colW * 0.62;
    $plotH = 84;
    $baseY = 100;
@endphp
<svg {{ $attributes->merge(['class' => 'w-full h-auto']) }} viewBox="0 0 280 124" role="img" aria-label="直近 7 日の歩数グラフ" xmlns="http://www.w3.org/2000/svg">
    @foreach ($days as $i => $day)
        @php
            $v = $values[$i];
            $h = round($v / $max * $plotH, 1);
            $x = round($i * $colW + ($colW - $barW) / 2, 1);
            $cx = round($i * $colW + $colW / 2, 1);
            $isToday = $i === $count - 1;
            $reached = $v >= (int) $goal && (int) $goal > 0;
        @endphp
        <g data-date="{{ $day->date }}" data-steps="{{ $v }}">
            <title>{{ $day->date }}：{{ number_format($v) }} 歩</title>
            <rect x="{{ $x }}" y="{{ $baseY - max($h, 1.5) }}" width="{{ round($barW, 1) }}" height="{{ max($h, 1.5) }}" rx="3" class="{{ $reached ? 'fill-accent' : 'fill-bar' }}" />
            <text x="{{ $cx }}" y="{{ $baseY - max($h, 1.5) - 3 }}" text-anchor="middle" font-size="8" class="fill-sub">{{ $v > 0 ? number_format($v / 1000, 1).'k' : '-' }}</text>
            <text x="{{ $cx }}" y="116" text-anchor="middle" font-size="9" class="fill-sub {{ $isToday ? 'font-bold' : '' }}">{{ $isToday ? '今日' : (int) substr($day->date, 8, 2) }}</text>
        </g>
    @endforeach
</svg>
