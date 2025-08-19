<?php

namespace App\Http\Controllers;

use App\Http\Requests\Module\Question\StoreRequest;
use App\Http\Requests\Module\Question\UpdateRequest;
use App\Models\Module;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModuleQuestionController extends Controller
{
    public function store(StoreRequest $request, Module $module): RedirectResponse
    {
        try {
            $input = $request->validated();

            $question = $input['question'];
            if ($question instanceof UploadedFile) {
                $question = Storage::url($question->storePublicly('modules/' . $module->id . '/files'));
            }

            $choices = $input['choices'];
            foreach ($choices as $key => $choice) {
                if ($choice instanceof UploadedFile) {
                    $choices[$key] = Storage::url($choice->storePublicly('modules/' . $module->id . '/files'));
                }
            }

            $module->questions()->create([
                'title' => $input['title'],
                'question' => $question,
                'choices' => $choices,
                'correct_answer_index' => $input['correct_answer_index'],
            ]);

            return back()->with('success', __('action.created', ['menu' => __('menu.module_question')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function update(UpdateRequest $request, Module $module, Question $question): RedirectResponse
    {
        try {
            $input = $request->validated();
            $question->update([
                'title' => $input['title'],
                'question' => $input['question'],
                'choices' => $input['choices'],
                'correct_answer_index' => $input['correct_answer_index'],
            ]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.module_question')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Module $module, Question $question): RedirectResponse
    {
        try {
            $question->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.module_question')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function massDestroy(Request $request, Module $module): RedirectResponse
    {
        try {
            $ids = $request->input('ids');
            if (empty($ids)) {
                throw new \Exception('Empty ids');
            }

            Question::query()
                ->whereIn('id', $ids)
                ->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.module_question')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
