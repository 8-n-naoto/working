<x-app-layout>
    <div class="py-3">
        <a class="rounded-full border border-line bg-card px-3 py-1.5 text-[13px]" href="{{ route('groups.ranking', $group) }}">← ランキング</a>
    </div>

    <div class="mb-3 flex items-center gap-3 rounded-2xl border border-line bg-card p-4">
        <div class="flex h-14 w-14 flex-none items-center justify-center rounded-full bg-accent text-[22px] font-bold text-white">{{ mb_substr($member->name, 0, 1) }}</div>
        <div class="min-w-0">
            <h1 class="truncate text-xl font-bold">{{ $member->name }}</h1>
            <p class="truncate text-[13px] text-sub">{{ $group->name }}</p>
        </div>
    </div>

    @php
        $total = array_sum(array_map(fn ($d) => $d->steps, $days));
        $today = end($days)->steps;
        $avg = count($days) > 0 ? (int) round($total / count($days)) : 0;
    @endphp
    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <div class="flex justify-between">
            <div><p class="text-[13px] text-sub">今日</p><b class="text-[22px]">{{ number_format($today) }}</b></div>
            <div><p class="text-[13px] text-sub">7日合計</p><b class="text-[22px]">{{ number_format($total) }}</b></div>
            <div><p class="text-[13px] text-sub">7日平均</p><b class="text-[22px]">{{ number_format($avg) }}</b></div>
        </div>
        <div class="mt-3"><x-step-chart :days="$days" :goal="(int) config('steps.daily_goal')" /></div>
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card px-4 py-2">
        @foreach ($days as $day)
            <div data-date="{{ $day->date }}" class="flex items-center justify-between border-b border-line py-2 text-sm last:border-b-0">
                <span>{{ $day->date }}</span>
                <span>
                    @if ($day->source === \App\Enums\StepSource::Manual)
                        <span class="text-xs">{{ $day->source->label() }}</span>
                    @endif
                    <b>{{ number_format($day->steps) }}</b> 歩
                </span>
            </div>
        @endforeach
    </div>

    @can('removeMember', [$group, $member])
        <div class="mb-3 rounded-2xl border border-line bg-card p-4">
            <form method="POST" action="{{ route('groups.members.destroy', [$group, $member]) }}" x-data @submit="if (! confirm('このメンバーをグループから外しますか？招待コードも再発行されます。')) $event.preventDefault()">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-warn underline">このメンバーをグループから外す</button>
                <p class="mt-1 text-[13px] text-sub">外すと招待コードが再発行され、今のコードでは参加できなくなります。歩数の記録は消えません。</p>
            </form>
        </div>
    @endcan
</x-app-layout>
