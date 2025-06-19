<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Show the password reset page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'token' => 'required',
            'email' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $input['email'])
                ->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'Phone number or email not found.']);
            }

            if (!Hash::check($input['token'], $resetRecord->token)) {
                return back()->withErrors(['email' => 'Invalid token.']);
            }

            if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
                return back()->withErrors(['email' => 'Token expired.']);
            }

            $user = User::query()
                ->where('email', $input['email'])
                ->orWhere('phone', $input['email'])
                ->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            $user->forceFill([
                'password' => Hash::make($input['password']),
                'remember_token' => Str::random(60),
            ])->save();

            DB::table('password_reset_tokens')
                ->where('email', $input['email'])
                ->delete();

            $user->notify(new \App\Notifications\User\PasswordChanged());
            event(new PasswordReset($user));

            return to_route('login')->with('status', 'Success! Your password has been changed. Please login with your new password.');
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->withErrors(['email' => $exception->getMessage()]);
        }
    }
}
