<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Look up the user first so we can give specific feedback on status.
        $user = User::where('username', $this->input('student_number'))->first();

        if (! $user || ! Auth::validate(['username' => $this->input('student_number'), 'password' => $this->input('password')])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'student_number' => trans('auth.failed'),
            ]);
        }

        if ($user->status === 'pending') {
            throw ValidationException::withMessages([
                'student_number' => 'Your registration is still pending admin approval. Please wait for an email with your login credentials.',
            ]);
        }

        if ($user->status === 'rejected') {
            throw ValidationException::withMessages([
                'student_number' => 'Your registration was not approved. Please contact the ROTC office for more information.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'student_number' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('student_number')).'|'.$this->ip());
    }
}