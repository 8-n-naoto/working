<x-app-layout>
    <div class="flex items-center justify-between py-3">
        <h1 class="text-xl font-bold">今日</h1>
        @if ($group && $groups->count() > 1)
            <nav class="flex max-w-[60%] flex-wrap justify-end gap-1">
                @foreach ($groups as $g)
                    <a href="{{ route('home', ['group' => $g->id]) }}"
                       class="truncate rounded-full border border-line px-3 py-1 text-[13px] {{ $g->id === $group->id ? 'bg-accent-weak font-bold text-accent' : 'bg-card' }}">{{ $g->name }}</a>
                @endforeach
            </nav>
        @endif
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <p id="today-steps" class="text-[40px] font-bold leading-tight tracking-wide">{{ number_format($todaySteps) }}<span class="text-lg"> 歩</span></p>
        <div class="my-2 h-2.5 overflow-hidden rounded-full bg-line">
            <div class="h-full rounded-full bg-accent" style="width: {{ $goal > 0 ? min(100, round($todaySteps / $goal * 100)) : 0 }}%"></div>
        </div>
        <p id="goal-remaining" class="text-[13px] text-sub">
            @if ($remaining === 0)
                目標 {{ number_format($goal) }} 歩 達成！🎉
            @else
                目標 {{ number_format($goal) }} 歩まであと {{ number_format($remaining) }} 歩
            @endif
        </p>
    </div>

    @if ($group)
        <a href="{{ route('groups.ranking', $group) }}" class="mb-3 flex items-center justify-between rounded-2xl border border-line bg-card p-4">
            <div>
                <p class="text-[13px] text-sub">{{ $group->name }}</p>
                <p id="today-rank" class="text-[28px] font-bold">今日の順位：{{ $rank }} 位</p>
            </div>
            @if ($gap !== null)
                <p id="rank-gap" class="text-right text-[13px] text-sub">1 つ上の人との差<br><b class="text-ink">{{ number_format($gap) }} 歩</b></p>
            @else
                <p class="text-right text-[13px] text-sub">トップです 👑</p>
            @endif
        </a>
    @else
        <div class="mb-3 rounded-2xl border border-line bg-card p-4">
            <p id="no-group">まだグループに所属していません。</p>
            <a class="mt-2 block text-sm font-semibold text-accent underline" href="{{ route('groups.index') }}">グループを作成・参加する</a>
        </div>
    @endif

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>直近 7 日間</b>
        <div id="week-chart" class="mt-2">
            <x-step-chart :days="$days" :goal="$goal" />
        </div>
    </div>

    <a href="{{ route('link.show') }}" class="mt-2 flex min-h-[46px] w-full items-center justify-center rounded-xl bg-accent-weak text-base font-semibold text-accent">歩数を登録・連携設定</a>
</x-app-layout>
