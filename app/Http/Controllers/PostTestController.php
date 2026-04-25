<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleAssessee;
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

    public function grade(Request $request, PostTest $test): RedirectResponse
    {
        try {
            $request->validate([
                'essay_grades' => 'required|array',
                'essay_grades.*.id' => 'required|integer',
                'essay_grades.*.is_correct' => 'required|boolean',
            ]);

            $answers = $test->answers;
            $gradesMap = collect($request->input('essay_grades'))->keyBy('id');

            foreach ($answers as $i => $answer) {
                if ($answer['is_correct'] !== null) continue;
                $grade = $gradesMap->get($answer['id']);
                if ($grade !== null) {
                    $answers[$i]['is_correct'] = (bool) $grade['is_correct'];
                }
            }

            $totalQuestions = count($answers);
            $totalCorrect = count(array_filter($answers, fn ($a) => $a['is_correct'] === true));
            $score = $totalQuestions > 0 ? (int) ($totalCorrect / $totalQuestions * 100) : 0;

            $test->update([
                'answers' => $answers,
                'score' => $score,
                'is_graded' => true,
                'is_passed' => $score >= $test->minimum_score,
            ]);

            ModuleAssessee::where('module_id', $test->module_id)
                ->where('user_id', $test->user_id)
                ->update(['score' => $score]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.post_test')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
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
