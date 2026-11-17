<?php

namespace App\Http\Requests\Auth;

use App\Services\RateLimitIp;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            RateLimiter::hit($this->ipThrottleKey(), 60 * (int) config('steps.rate.login_ip_decay_minutes'));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // IP キーは clear しない（D-024：成功ログインで IP 単位の失敗回数を消せないようにする）
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        foreach ([
            [$this->ipThrottleKey(), (int) config('steps.rate.login_ip_max_failures')],
            [$this->throttleKey(), 5],
        ] as [$key, $max]) {
            if (! RateLimiter::tooManyAttempts($key, $max)) {
                continue;
            }

            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.RateLimitIp::normalize($this->ip()));
    }

    /**
     * IP 単位の失敗回数用キー（D-024。IPv6 は /64 単位）。
     */
    public function ipThrottleKey(): string
    {
        return 'login-ip:'.RateLimitIp::normalize($this->ip());
    }
}
