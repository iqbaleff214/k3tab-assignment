<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $items = $request->user()
            ->notifications()
            ->paginate($request->query('size', 50))
            ->withQueryString();
        foreach ($items as $notification) {
            if (is_null($notification->read_at)) {
                $notification->markAsRead();
                $notification->read_at = null;
            }
        }

        return Inertia::render('notification/Index', [
            'items' => $items,
        ]);
    }

    public function markAsRead(Request $request, $id): RedirectResponse
    {
        try {
            $request->user()->notifications()->where('id', $id)->first()->markAsRead();

            return back()->with('success', __('action.updated', ['menu' => __('menu.notification')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function markAsReadAll(Request $request): RedirectResponse
    {
        try {
            $request->user()->unreadNotifications->markAsRead();

            return back()->with('success', __('action.updated', ['menu' => __('menu.notification')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
    public function destroy(Request $request, $id): RedirectResponse
    {
        try {
            $request->user()->notifications()->where('id', $id)->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.notification')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
