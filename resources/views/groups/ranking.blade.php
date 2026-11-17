<x-app-layout>
    <div class="flex items-center justify-between py-3">
        <h1 class="min-w-0 truncate text-xl font-bold">{{ $group->name }} のランキング</h1>
    </div>

    @if ($groups->count() > 1)
        <nav class="mb-3 flex flex-wrap gap-1">
            @foreach ($groups as $g)
                <a href="{{ route('groups.ranking', ['group' => $g, 'period' => $period->value]) }}"
                   class="truncate rounded-full border border-line px-3 py-1 text-[13px] {{ $g->id === $group->id ? 'bg-accent-weak font-bold text-accent' : 'bg-card' }}">{{ $g->name }}</a>
            @endforeach
        </nav>
    @endif

    <nav class="mb-3 flex rounded-[10px] bg-line p-[3px]">
        @foreach (\App\Enums\RankingPeriod::cases() as $p)
            <a href="{{ route('groups.ranking', ['group' => $group, 'period' => $p->value]) }}"
               class="flex min-h-[38px] flex-1 items-center justify-center rounded-lg text-sm {{ $p === $period ? 'bg-card font-bold shadow-sm' : '' }}">{{ $p->label() }}</a>
        @endforeach
    </nav>

    @php
        $rows = $board->rows;
        $top = array_slice($rows, 0, 3);
        // 表彰台は 2 位・1 位・3 位の並び
        $podium = array_values(array_filter([$top[1] ?? null, $top[0] ?? null, $top[2] ?? null]));
        $heights = [1 => 96, 2 => 70, 3 => 52];
        $rankColors = [1 => 'text-gold', 2 => 'text-silver', 3 => 'text-bronze'];
    @endphp

    @if (count($rows) > 0)
        <div class="mb-3 rounded-2xl border border-line bg-card p-4">
            <div class="flex items-end justify-center gap-2">
                @foreach ($podium as $row)
                    <a href="{{ route('groups.members.show', [$group, $row->userId]) }}" class="min-w-0 flex-1 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-accent font-bold text-white">{{ mb_substr($row->name, 0, 1) }}</div>
                        <div class="mt-1 truncate text-[13px]">{{ $row->name }}</div>
                        <div class="text-xs text-sub">{{ number_format($row->value) }}歩</div>
                        <div class="mt-1.5 flex items-center justify-center rounded-t-[10px] bg-accent-weak font-bold text-accent" style="height: {{ $heights[$row->rank] ?? 40 }}px">{{ $row->rank }}</div>
                    </a>
                @endforeach
            </div>
        </div>

        <div id="ranking-list" class="mb-3 rounded-2xl border border-line bg-card px-3 py-2">
            @foreach ($rows as $row)
                <div data-user-id="{{ $row->userId }}" class="flex min-h-[56px] items-center gap-3 border-b border-line px-1 py-3 last:border-b-0 {{ $row->userId === auth()->id() ? 'rounded-[10px] bg-accent-weak !px-2' : '' }}">
                    <div class="w-7 text-center text-[17px] font-bold {{ $rankColors[$row->rank] ?? '' }}">{{ $row->rank }}</div>
                    <div class="flex h-[38px] w-[38px] flex-none items-center justify-center rounded-full bg-accent font-bold text-white">{{ mb_substr($row->name, 0, 1) }}</div>
                    <a class="min-w-0 flex-1 truncate" href="{{ route('groups.members.show', [$group, $row->userId]) }}">{{ $row->name }}</a>
                    <div class="text-right font-bold">{{ number_format($row->value) }}<span class="text-[13px] font-normal text-sub"> 歩</span></div>
                </div>
            @endforeach
        </div>
    @else
        <p class="rounded-2xl border border-line bg-card p-4 text-sm text-sub">メンバーがいません。</p>
    @endif

    <p class="text-[13px] text-sub">※ 未送信のメンバーは 0 歩として表示されます</p>
</x-app-layout>
