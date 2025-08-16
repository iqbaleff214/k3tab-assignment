<?php

namespace App\Http\Controllers;

use App\Http\Requests\Module\Question\StoreRequest;
use App\Http\Requests\Module\Question\UpdateRequest;
use App\Models\Module;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModuleQuestionController extends Controller
{
    public function store(StoreRequest $request, Module $module): RedirectResponse
    {
        try {
            $input = $request->validated();
            $module->questions()->create([
                'title' => $input['title'],
                'question' => $input['question'],
                'choices' => $input['choices'],
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
