<x-app-layout>
    <h1 class="py-3 text-xl font-bold">グループ</h1>

    <div class="mb-3 rounded-2xl border border-line bg-card px-3 py-2">
        @forelse ($groups as $group)
            <div class="flex items-center gap-3 border-b border-line py-3 last:border-b-0">
                <div class="flex h-[38px] w-[38px] flex-none items-center justify-center rounded-full bg-accent font-bold text-white">{{ mb_substr($group->name, 0, 1) }}</div>
                <div class="min-w-0 flex-1">
                    <div class="truncate">{{ $group->name }}</div>
                    @can('regenerateInviteCode', $group)
                        <div class="text-[13px] text-sub">招待コード <b class="font-mono text-ink">{{ $group->invite_code }}</b></div>
                        <form method="POST" action="{{ route('groups.invite-code.regenerate', $group) }}">
                            @csrf
                            <button type="submit" class="text-[13px] text-accent underline">招待コードを再発行する</button>
                        </form>
                    @endcan
                    @can('leave', $group)
                        <form method="POST" action="{{ route('groups.leave', $group) }}" x-data @submit="if (! confirm('このグループから退会しますか？')) $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[13px] text-sub underline">このグループから退会する</button>
                        </form>
                    @endcan
                </div>
                <a href="{{ route('groups.ranking', $group) }}" class="rounded-full border border-line px-3 py-1 text-[13px]">見る</a>
            </div>
        @empty
            <p class="py-3 text-sm text-sub">まだグループに所属していません。</p>
        @endforelse
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>招待コードで参加</b>
        <form method="POST" action="{{ route('groups.join') }}">
            @csrf
            <x-input-label for="invite_code" value="招待コード（6桁）" />
            <x-text-input id="invite_code" name="invite_code" type="text" :value="old('invite_code')" maxlength="6" required class="uppercase" placeholder="例：ABC123" />
            <x-input-error :messages="$errors->get('invite_code')" class="mt-2" />
            <x-primary-button>参加する</x-primary-button>
        </form>
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>新しいグループを作る</b>
        <form method="POST" action="{{ route('groups.store') }}">
            @csrf
            <x-input-label for="name" value="グループ名" />
            <x-text-input id="name" name="name" type="text" :value="old('name')" maxlength="50" required placeholder="例：同期の歩こう会" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <x-primary-button class="!bg-accent-weak !text-accent">作成して招待コードを発行</x-primary-button>
        </form>
    </div>
</x-app-layout>
