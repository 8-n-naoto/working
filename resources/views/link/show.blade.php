<x-app-layout>
    <h1 class="py-3 text-xl font-bold">歩数の登録</h1>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <div class="flex items-center justify-between">
            <b>iPhone ショートカット連携</b>
            @if ($hasToken)
                <span class="rounded-full bg-accent-weak px-2 py-0.5 text-xs text-accent">連携中</span>
            @else
                <span class="rounded-full border border-warn px-2 py-0.5 text-xs text-warn">未連携</span>
            @endif
        </div>
        <p id="last-received" class="mt-2 text-[13px] text-sub">
            最終受信：{{ $lastUsedAt ? $lastUsedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') : '未受信' }}
        </p>
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>初回設定（1回だけ）</b>
        <ol class="mt-2 list-decimal space-y-2 pl-5 text-sm">
            <li>専用ショートカットを iPhone に作成（手順書の通り）</li>
            <li>初回実行時に「ヘルスケア（歩数）」の読み取りを許可</li>
            <li>下の連携トークンをショートカットに貼り付け</li>
            <li>オートメーションで「毎日21:00」などに設定し「すぐに実行」をオン</li>
        </ol>

        <p class="mb-1 mt-4 text-[13px] text-sub">あなたの連携トークン</p>
        @if ($plainToken)
            <div x-data="{ copied: false }">
                <p class="mb-1 text-[13px] text-warn">この画面を閉じると再表示できません。今すぐコピーしてください。</p>
                <code id="step-token" class="block break-all rounded-lg border border-dashed border-line bg-app p-2.5 font-mono text-[13px]">{{ $plainToken }}</code>
                <button type="button"
                        class="mt-2 block min-h-[46px] w-full rounded-xl bg-accent-weak text-base font-semibold text-accent"
                        x-on:click="navigator.clipboard.writeText(document.getElementById('step-token').textContent.trim()).then(() => copied = true)">
                    コピー
                </button>
                <span x-show="copied" x-cloak class="text-sm text-accent">コピーしました</span>
            </div>
        @elseif ($hasToken)
            <p class="rounded-lg border border-dashed border-line bg-app p-2.5 text-[13px]">発行済み（セキュリティのため再表示できません）</p>
        @else
            <p class="rounded-lg border border-dashed border-line bg-app p-2.5 text-[13px]">未発行</p>
        @endif

        <form method="POST" action="{{ route('link.token.store') }}">
            @csrf
            <button type="submit" class="mt-2 block min-h-[46px] w-full rounded-xl border border-line text-base font-semibold">
                {{ $hasToken ? '再発行する' : '発行する' }}
            </button>
        </form>
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>手動で入力</b>
        <p class="mt-1 text-[13px] text-sub">連携できない日の補正用です。</p>
        <form method="POST" action="{{ route('steps.manual.store') }}">
            @csrf
            <x-input-label for="date" value="日付" />
            <x-text-input id="date" name="date" type="date" :value="old('date', $today)" required />
            <x-input-error :messages="$errors->get('date')" class="mt-1" />
            <x-input-label for="steps" value="歩数" />
            <x-text-input id="steps" name="steps" type="text" inputmode="numeric" :value="old('steps')" placeholder="例：8500" required />
            <x-input-error :messages="$errors->get('steps')" class="mt-1" />
            <x-primary-button>保存する</x-primary-button>
        </form>
    </div>

    <div class="mb-3 rounded-2xl border border-line bg-card p-4">
        <b>直近 7 日の履歴</b>
        <div class="mt-2"><x-step-chart :days="$days" :goal="(int) config('steps.daily_goal')" /></div>
        <ul id="step-history" class="mt-2 text-sm">
            @foreach ($days as $day)
                <li data-date="{{ $day->date }}" class="flex justify-between border-b border-line py-1.5 last:border-b-0">
                    <span>{{ $day->date }}</span>
                    <span>
                        @if ($day->source === \App\Enums\StepSource::Manual)
                            <span class="text-xs">{{ $day->source->label() }}</span>
                        @endif
                        {{ number_format($day->steps) }} 歩
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
