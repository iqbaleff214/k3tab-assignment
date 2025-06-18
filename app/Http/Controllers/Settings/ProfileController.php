<?php

namespace App\Http\Controllers\Settings;

use App\Events\User\NewPhoneNumber;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $isPhoneChanged = $request->user()->isDirty('phone');

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->user()->isDirty('locale')) {
            Session::put('locale', $request->user()->locale);
            App::setLocale($request->user()->locale);
            $user = $request->user();
            activity()
                ->useLog(\App\Models\User::class)
                ->causedBy($user)
                ->performedOn($user)
                ->event('auth')
                ->log(__('activity.user.locale', [
                    'identifier' => $user->name,
                    'locale' => $user->locale,
                    'link' => '#',
                ]));
        }

        $request->user()->save();
        if ($isPhoneChanged && $request->user()->phone) {
            event(new NewPhoneNumber($request->user()));
        }

        return to_route('profile.edit')
            ->with('success', __('action.updated', ['menu' => __('menu.profile')]));
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
