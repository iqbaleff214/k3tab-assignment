<?php

namespace App\Http\Controllers\Assessee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\PostTest\StorePostTest;
use App\Models\Media;
use App\Models\Module;
use App\Models\PostTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModuleController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        return Inertia::render('panel/assessee/module/Index', [
            'items' => Module::with(['questions','assessees' => fn ($query) => $query->where('user_id', $userId)])
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->get(),
        ]);
    }

    public function show(Request $request, Module $module): Response
    {
        $userId = $request->user()->id;

        if (!$module->assessees()->where('user_id', $userId)->exists()) {
            $module->assessees()->create([
                'user_id' => $userId,
                'read_at' => now(),
            ]);
        }

        $module->load([
            'assessees' => fn ($query) => $query->where('user_id', $userId),
            'media', 'questions',
        ]);
        return Inertia::render('panel/assessee/module/Show', [
            'item' => $module,
            'result_histories' => PostTest::query()
                ->where('module_id', $module->id)
                ->where('user_id', $userId)
                ->latest()
                ->get(),
        ]);
    }

    public function download(Request $request, Module $module, Media $media): StreamedResponse|RedirectResponse
    {
        try {
            $assessee = $module->assessees()->where('user_id', $request->user()->id)->first();
            if ($assessee->is_doing_test)
                abort(403, 'You are doing test');

            if (!in_array($media->path_url, $assessee->downloaded_files ?? [])) {
                $files = $assessee->downloaded_files ?? [];
                $files[] = $media->path_url;
                $assessee->downloaded_files = $files;
                $assessee->save();
            }

            return Storage::download($media->path, $request->input('filename'));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function startPostTest(Request $request, Module $module): RedirectResponse
    {
        try {
            $assessee = $module->assessees()->where('user_id', $request->user()->id)->first();
            if ($assessee->is_doing_test)
                abort(403, 'Test already started');

            $assessee->is_doing_test = true;
            $assessee->save();

            return back();
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function finishPostTest(StorePostTest $request, Module $module): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessee = $module->assessees()->where('user_id', $request->user()->id)->first();
            if (!$assessee->is_doing_test)
                abort(403, 'You are not doing test');

            $module->load(['questions']);

            $totalQuestions = count($input['answers']);
            $totalCorrect = 0;
            foreach ($input['answers'] as $index => $answer) {
                $question = $module->questions->where('id', $answer['id'])->first();
                $input['answers'][$index]['answer'] = $question->choices[$answer['answer_index']];
                $isCorrect = $question->correct_answer_index == $answer['answer_index'];
                $input['answers'][$index]['is_correct'] = $isCorrect;

                if ($isCorrect) {
                    $totalCorrect++;
                }
            }
            $input['score'] = $totalCorrect / $totalQuestions * 100;

            $module->postTests()->create([
                'user_id' => $request->user()->id,
                'answers' => $input['answers'],
                'score' => $input['score'],
                'minimum_score' => $module->minimum_score,
                'is_passed' => $input['score'] >= $module->minimum_score,
            ]);

            $assessee->score = $input['score'];
            $assessee->is_doing_test = false;
            $assessee->save();

            return back();
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function cancelPostTest(Request $request, Module $module): RedirectResponse
    {
        try {
            $assessee = $module->assessees()->where('user_id', $request->user()->id)->first();
            if (!$assessee->is_doing_test)
                abort(403, 'You are not doing test');

            $assessee->is_doing_test = false;
            $assessee->save();

            return back();
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
