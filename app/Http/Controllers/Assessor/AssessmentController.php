<?php

namespace App\Http\Controllers\Assessor;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\EvaluateRequest;
use App\Http\Requests\Assessment\ProposalRequest;
use App\Models\Assessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function proposal(ProposalRequest $request, Assessment $assessment): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessment->update([
                'scheduled_at' => $input['scheduled_at'],
                'status' => empty($input['assessment_scheduled_id']) ?
                    AssessmentStatus::Cancelled->value : AssessmentStatus::Scheduled->value,
                'tasks' => $assessment->guide->tasks,
            ]);

            $assessment->schedules()->update([ 'status' => 0 ]);
            if (!empty($input['assessment_scheduled_id'])) {
                $assessment->schedules()
                    ->where('id', $input['assessment_scheduled_id'])
                    ->update([ 'status' => 1 ]);
            }

            return back()->with('success', __('action.updated', ['menu' => __('menu.assessment')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function store(EvaluateRequest $request, Assessment $assessment): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessment->update([
                'status' => AssessmentStatus::Completed->value,
                'tasks' => $input['tasks'],
                'result' => empty($input['result']) ?
                    AssessmentResult::NotCompetent->value : AssessmentResult::Competent->value,
                'comment' => $input['comment'],
                'started_at' => $input['started_at'],
                'finished_at' => now(),
            ]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.assessment')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function print(Assessment $assessment): View
    {
        return view('pdf.assessor-assessment', compact('assessment'));
    }
}
