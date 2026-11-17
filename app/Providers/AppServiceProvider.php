<?php

namespace App\Providers;

use App\Rules\MaxBytes;
use App\Services\RateLimitIp;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('steps-ip', fn (Request $request) => Limit::perMinute((int) config('steps.rate.api_ip_per_minute'))
            ->by('ip:'.RateLimitIp::normalize($request->ip())));

        RateLimiter::for('steps-user', fn (Request $request) => Limit::perHour((int) config('steps.rate.api_user_per_hour'))
            ->by('user:'.$request->user()->id));

        RateLimiter::for('group-join', fn (Request $request) => [
            Limit::perMinute((int) config('steps.rate.group_join_per_minute'))
                ->by('user:'.$request->user()->id),
            Limit::perMinute((int) config('steps.rate.group_join_ip_per_minute'))
                ->by('ip:'.RateLimitIp::normalize($request->ip())),
        ]);

        RateLimiter::for('register', fn (Request $request) => Limit::perHour((int) config('steps.rate.register_ip_per_hour'))
            ->by('ip:'.RateLimitIp::normalize($request->ip())));

        // D-031：ログイン後の書き込み操作（ユーザー単位）
        RateLimiter::for('group-create', fn (Request $request) => Limit::perHour((int) config('steps.rate.group_create_per_hour'))
            ->by('user:'.$request->user()->id));

        RateLimiter::for('token-issue', fn (Request $request) => Limit::perHour((int) config('steps.rate.token_issue_per_hour'))
            ->by('user:'.$request->user()->id));

        RateLimiter::for('manual-steps', fn (Request $request) => Limit::perMinute((int) config('steps.rate.manual_steps_per_minute'))
            ->by('user:'.$request->user()->id));

        // D-030：bcrypt は先頭 72 バイトのみ照合に使うため、新規登録で 72 バイトを超えるパスワードを弾く
        Password::defaults(fn () => Password::min(8)->rules([new MaxBytes(72)]));
    }
}
