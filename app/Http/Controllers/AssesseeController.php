<?php

namespace App\Http\Controllers;

use App\Enum\UserType;
use App\Events\User\NewUserCreated;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Mail\AccountCreatedMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AssesseeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('assessee/Index', [
            'items' => User::query()
                ->where('type', UserType::Assessee)
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->render($request->query('size')),
         ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $input = $request->validated();

            $password = Str::random(8);
            $assessee = User::query()
                ->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($password),
                    'phone' => $input['phone'],
                    'type' => UserType::Assessee,
                ]);
            Mail::to($input['email'])
                ->send(new AccountCreatedMail($assessee, $password));

            return back()->with('success', __('action.created', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $assessee): Response
    {
        return Inertia::render('assessee/Show', [
            'item' => $assessee,
            'next' => User::query()->where('type', UserType::Assessee)
                ->where('id', '>', $assessee->id)
                ->value('id'),
            'prev' => User::query()->where('type', UserType::Assessee)
                ->where('id', '<', $assessee->id)
                ->orderByDesc('created_at')->value('id'),
            'total' => User::query()->where('type', UserType::Assessee)->count(),
            'index' => User::query()->where('type', UserType::Assessee)
                    ->where('id', '<', $assessee->id)->count('id') + 1,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $assessee): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessee->update([
                'name' => $input['name'],
                'email' => $input['email'] ?? $assessee->email,
                'phone' => $input['phone'],
            ]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $assessee): RedirectResponse
    {
        try {
            $assessee->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function massDestroy(Request $request): RedirectResponse
    {
        try {
            $ids = $request->input('ids');
            if (empty($ids)) {
                throw new \Exception('Empty ids');
            }

            User::query()
                ->whereIn('id', $ids)
                ->where('type', UserType::Assessee)
                ->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
