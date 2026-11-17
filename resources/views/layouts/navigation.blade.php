<header class="flex items-center justify-between px-4 pb-1 pt-4">
    <a href="{{ route('home') }}" class="text-sm font-bold text-accent">👟 {{ config('app.name') }}</a>
    <div class="flex items-center gap-3 text-xs text-sub">
        <span class="max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-full border border-line px-3 py-1 text-ink">ログアウト</button>
        </form>
    </div>
</header>
