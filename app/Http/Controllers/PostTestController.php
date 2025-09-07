<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\PostTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PostTestController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('post_test/Index', [
            'items' => PostTest::with(['module', 'assessee'])
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->render($request->query('size')),
            'modules' => Module::all(),
        ]);
    }

    public function destroy(Request $request, PostTest $test): RedirectResponse
    {
        try {
            $test->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.post_test')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
