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
            $path = 'modules/' . $module->id . '/files';

            $questionImage = null;
            if (($input['question_image'] ?? null) instanceof UploadedFile) {
                $questionImage = Storage::url($input['question_image']->storePublicly($path));
            }

            $choices = $input['choices'] ?? null;
            $choicesImages = null;

            if ($choices !== null) {
                foreach ($choices as $key => $choice) {
                    if ($choice instanceof UploadedFile) {
                        $choices[$key] = Storage::url($choice->storePublicly($path));
                    }
                }
            }

            if (!empty($input['choices_images'])) {
                $choicesImages = [];
                foreach ($input['choices_images'] as $key => $img) {
                    $choicesImages[$key] = ($img instanceof UploadedFile)
                        ? Storage::url($img->storePublicly($path))
                        : null;
                }
            }

            $module->questions()->create([
                'title' => $input['title'] ?? null,
                'type' => $input['type'],
                'question' => $input['question'] ?? null,
                'question_image' => $questionImage,
                'choices' => $choices,
                'choices_images' => $choicesImages,
                'correct_answer_index' => $input['correct_answer_index'] ?? null,
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
            $path = 'modules/' . $module->id . '/files';

            $questionImage = $question->question_image;
            if (($input['question_image'] ?? null) instanceof UploadedFile) {
                $questionImage = Storage::url($input['question_image']->storePublicly($path));
            }

            $choices = $input['choices'] ?? null;
            $choicesImages = null;

            if (isset($input['choices_images'])) {
                $choicesImages = [];
                foreach ($input['choices_images'] as $key => $img) {
                    if ($img instanceof UploadedFile) {
                        $choicesImages[$key] = Storage::url($img->storePublicly($path));
                    } elseif (is_string($img) && $img !== '') {
                        $choicesImages[$key] = $img;
                    } else {
                        $choicesImages[$key] = null;
                    }
                }
            }

            $question->update([
                'title' => $input['title'] ?? null,
                'type' => $input['type'],
                'question' => $input['question'] ?? null,
                'question_image' => $questionImage,
                'choices' => $choices,
                'choices_images' => $choicesImages,
                'correct_answer_index' => $input['correct_answer_index'] ?? null,
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
