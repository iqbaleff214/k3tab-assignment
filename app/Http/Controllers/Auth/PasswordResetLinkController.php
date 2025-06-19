<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required',
        ]);

        $emailOrPhone = $request->input('email');
        $column = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::query()
            ->where($column, $emailOrPhone)
            ->first();
        if (!$user) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }

        if ($column === 'email') {
            Password::sendResetLink($request->only('email'));
        } else {
            $token = Str::random(64);
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(['email' => $emailOrPhone], [
                'email' => $emailOrPhone,
                'token' => bcrypt($token),
                'created_at' => now(),
            ]);

            $resetLink = url(route('password.reset', [
                'token' => $token,
                'email' => $emailOrPhone,
            ], false));
        }
        $user->notify(new \App\Notifications\User\ForgotPassword(now(), $request->ip(), $resetLink ?? null));


        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
