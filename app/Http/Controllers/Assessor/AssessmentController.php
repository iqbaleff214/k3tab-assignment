<?php

namespace App\Http\Controllers\Assessor;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\EvaluateRequest;
use App\Http\Requests\Assessment\ProposalRequest;
use App\Models\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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
                'feedback' => $input['feedback'] ?? null,
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
                'assessee_name' => $input['assessee_name'] ?? null,
                'assessee_signature' => $input['assessee_signature'] ?? null,
                'assessee_signature_date' => $input['assessee_signature_date'] ?? null,
                'assessor_name' => $input['assessor_name'] ?? null,
                'assessor_signature' => $input['assessor_signature'] ?? null,
                'assessor_signature_date' => $input['assessor_signature_date'] ?? null,
                'supervisor_name' => $input['supervisor_name'] ?? null,
                'supervisor_signature' => $input['supervisor_signature'] ?? null,
                'supervisor_signature_date' => $input['supervisor_signature_date'] ?? null,
                'data_recorder_name' => $input['data_recorder_name'] ?? null,
                'data_recorder_signature' => $input['data_recorder_signature'] ?? null,
                'data_recorder_signature_date' => $input['data_recorder_signature_date'] ?? null,
            ]);

            $assessment->assessee->notify(new \App\Notifications\Assessment\AssessmentResult($assessment));

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

    public function downloadPrint(Assessment $assessment): Response
    {
        $pdf = Pdf::loadView('pdf.assessor-assessment', compact('assessment'));
        return $pdf->download( $assessment->guide?->skill_number . '.pdf');
    }
}
