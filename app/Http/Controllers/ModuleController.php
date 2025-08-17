<?php

namespace App\Http\Controllers;

use App\Http\Requests\Module\StoreRequest;
use App\Http\Requests\Module\UpdateRequest;
use App\Models\Media;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('module/Index', [
            'items' => Module::with(['media', 'questions'])
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

            DB::beginTransaction();
            $module = Module::query()->create([
                'title' => $input['title'],
                'description' => $input['description'],
                'body' => $input['body'],
                'duration_estimation' => $input['duration_estimation'],
                'minimum_score' => $input['minimum_score'],
                'questions_count' => $input['questions_count'],
                'code' => $input['code'],
                'equipment_required' => $input['equipment_required'],
                'procedure' => $input['procedure'],
                'reference' => $input['reference'],
                'performance' => $input['performance'],
            ]);

            foreach ($input['questions'] ?? [] as $question) {
                $module->questions()->create([
                    'title' => $question['title'],
                    'question' => $question['question'],
                    'choices' => $question['choices'],
                    'correct_answer_index' => $question['correct_answer_index'],
                ]);
            }

            foreach ($input['files'] ?? [] as $index => $file) {
                $module->media()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $file->storePublicly('modules/' . $module->id . '/files'),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
            DB::commit();

            return back()->with('success', __('action.created', ['menu' => __('menu.module')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Module $module): Response
    {
        $module->load(['media', 'questions']);
        return Inertia::render('module/Show', [
            'item' => $module,
            'next' => Module::query()->where('id', '>', $module->id)->value('id'),
            'prev' => Module::query()->where('id', '<', $module->id)
                ->orderByDesc('created_at')->value('id'),
            'total' => Module::query()->count(),
            'index' => Module::query()->where('id', '<', $module->id)->count('id') + 1,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Module $module): RedirectResponse
    {
        try {
            $input = $request->validated();

            $module->update([
                'title' => $input['title'],
                'description' => $input['description'],
                'body' => $input['body'],
                'duration_estimation' => $input['duration_estimation'],
                'minimum_score' => $input['minimum_score'],
                'questions_count' => $input['questions_count'],
                'code' => $input['code'],
                'equipment_required' => $input['equipment_required'],
                'procedure' => $input['procedure'],
                'reference' => $input['reference'],
                'performance' => $input['performance'],
            ]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.module')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Module $module): RedirectResponse
    {
        try {
            foreach ($module->media as $media) {
                $media->delete();
            }

            $module->delete();

            $nextId = $request->input('prev') ?? $request->input('next');
            if ($nextId) {
                return redirect()
                    ->route('module.show', $nextId)
                    ->with('success', __('action.deleted', ['menu' => __('menu.module')]));
            }

            return back()->with('success', __('action.deleted', ['menu' => __('menu.module')]));
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

            $media = Media::query()
                ->whereIn('mediaable_id', $ids)
                ->where('mediaable_type', Module::class)
                ->get();
            foreach ($media as $item) {
                $item->delete();
            }

            Module::query()
                ->whereIn('id', $ids)
                ->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.module')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function uploadFile(Request $request, Module $module): RedirectResponse
    {
        try {
            foreach ($request->file('files') as $file) {
                $module->media()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $file->storePublicly('modules/' . $module->id . '/files'),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            return back()->with('success', __('action.updated', ['menu' => __('menu.module')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function removeFile(Request $request, Module $module, Media $media): RedirectResponse
    {
        try {
            $module->media()
                ->where('id', $media->id)
                ->delete();

            return back()->with('success', __('action.updated', ['menu' => __('menu.module')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
