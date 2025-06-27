<?php

namespace App\Http\Controllers\Configuration;

use App\Enum\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalFlow\StoreRequest;
use App\Http\Requests\ApprovalFlow\UpdateRequest;
use App\Models\ApprovalFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ApprovalFlowController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('configuration/ApprovalFlow', [
            'items' => ApprovalFlow::with(['children', 'role'])
                ->whereNull('parent_id')
                ->get(),
            'subjects' => Permission::module(),
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $input = $request->validated();
            ApprovalFlow::query()->create($input);

            return back()->with('success', __('action.created', ['menu' => __('menu.approval_flow')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function update(UpdateRequest $request, ApprovalFlow $approval): RedirectResponse
    {
        try {
            $input = $request->validated();
            $approval->update($input);

            return back()->with('success', __('action.updated', ['menu' => __('menu.approval_flow')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(ApprovalFlow $approval): RedirectResponse
    {
        try {
            $approval->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.approval_flow')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
