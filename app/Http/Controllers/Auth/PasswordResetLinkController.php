<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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
            'email' => 'required|email',
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();
        if ($user) {
            $user->notify(new \App\Notifications\User\ForgotPassword(now(), $request->ip()));
        }

        Password::sendResetLink(
            $request->only('email')
        );


        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
