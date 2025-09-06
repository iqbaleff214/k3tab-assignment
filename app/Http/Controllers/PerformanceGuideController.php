<?php

namespace App\Http\Controllers;

use App\Enum\TaskStatus;
use App\Http\Requests\PerformanceGuide\StoreRequest;
use App\Http\Requests\PerformanceGuide\UpdateRequest;
use App\Models\Module;
use App\Models\PerformanceGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceGuideController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('performance_guide/Index', [
            'items' => PerformanceGuide::query()
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->render($request->query('size')),
            'status' => TaskStatus::values(),
            'modules' => Module::all(),
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $input = $request->validated();
            foreach ($input['tasks'] as $taskIndex => $task) {
                foreach ($task['child'] as $childIndex => $child) {
                    if ($child['hint'] instanceof UploadedFile) {
                        $input['tasks'][$taskIndex]['child'][$childIndex]['hint'] = Storage::url($child['hint']->storePublicly('performance-guides'));
                    }
                }
            }
            PerformanceGuide::query()->create($input);

            return back()->with('success', __('action.created', ['menu' => __('menu.performance_guide')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error',$exception->getMessage());
        }
    }

    public function print(PerformanceGuide $guide): View
    {
        return view('pdf.performance-guide-gemini', compact('guide'));
    }

    public function update(UpdateRequest $request, PerformanceGuide $guide): RedirectResponse
    {
        try {
            $input = $request->validated();
            foreach ($input['tasks'] as $taskIndex => $task) {
                foreach ($task['child'] as $childIndex => $child) {
                    if ($child['hint'] instanceof UploadedFile) {
                        $input['tasks'][$taskIndex]['child'][$childIndex]['hint'] = Storage::url($child['hint']->storePublicly('performance-guides'));
                    }
                }
            }
            $guide->update($input);

            return back()->with('success', __('action.updated', ['menu' => __('menu.performance_guide')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error',$exception->getMessage());
        }
    }

    public function destroy(PerformanceGuide $guide): RedirectResponse
    {
        try {
            $guide->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.performance_guide')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error',$exception->getMessage());
        }
    }
}
