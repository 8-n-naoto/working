<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="メールアドレス" class="!mt-0" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="パスワード" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="mt-3 flex items-center gap-2 text-sm text-sub">
            <input id="remember_me" type="checkbox" class="rounded border-line text-accent focus:ring-accent" name="remember">
            ログインしたままにする
        </label>

        <x-primary-button>ログイン</x-primary-button>
        <a href="{{ route('register') }}" class="mt-3 flex min-h-[46px] w-full items-center justify-center rounded-xl border border-line text-base">新規登録</a>
    </form>
</x-guest-layout>
