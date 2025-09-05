<?php

namespace App\Http\Controllers\Assessor;

use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\ProposalRequest;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
