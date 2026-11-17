@php
    $tabs = [
        ['route' => 'home', 'match' => ['home', 'groups.ranking', 'groups.members.show'], 'icon' => '🏠', 'label' => 'ホーム'],
        ['route' => 'link.show', 'match' => ['link.show'], 'icon' => '📲', 'label' => '登録'],
        ['route' => 'groups.index', 'match' => ['groups.index'], 'icon' => '👥', 'label' => 'グループ'],
    ];
@endphp
<nav class="fixed bottom-0 left-1/2 flex w-full max-w-[440px] -translate-x-1/2 border-t border-line bg-card pb-[env(safe-area-inset-bottom)]">
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="flex min-h-[60px] flex-1 flex-col items-center justify-center gap-0.5 text-[11px] {{ request()->routeIs(...$tab['match']) ? 'font-bold text-accent' : 'text-sub' }}">
            <span class="text-xl">{{ $tab['icon'] }}</span>{{ $tab['label'] }}
        </a>
    @endforeach
</nav>
