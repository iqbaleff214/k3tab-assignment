<?php

namespace App\Http\Controllers\Assessee;

use App\Enum\AssessmentStatus;
use App\Enum\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\StoreRequest;
use App\Models\Assessment;
use App\Models\PerformanceGuide;
use App\Models\User;
use App\Notifications\Assessment\AssessorScheduleSubmission;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        return Inertia::render('panel/assessee/assessment/Index', [
            'items' => Assessment::with(['assessor', 'guide', 'guide.module'])
                ->where('assessee_id', $userId)
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->render($request->query('size')),
            'assessors' => User::query()
                ->where('type', UserType::Assessor)
                ->get(),
            'guides' => PerformanceGuide::with([
                    'module', 'module.assessees' => fn ($query) => $query->where('user_id', $userId)
                ])->get(),
        ]);
    }

    public function show(Request $request, Assessment $assessment): Response
    {
        $userId = $request->user()->id;
        if ($assessment->assessee_id !== $userId)
            abort(403);

        return Inertia::render('panel/assessee/assessment/Show', [
            'item' => $assessment,
        ]);
    }

    public function schedule(Request $request): Response
    {
        return Inertia::render('panel/assessee/assessment/Schedule', [

        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $input = $request->validated();

            $guide = PerformanceGuide::query()
                ->where('skill_number', $input['skill_number'])
                ->firstOrFail();

            $assessment = Assessment::query()->create([
                'assessor_id' => $input['assessor_id'],
                'assessee_id' => $user->id,
                'assessee_name' => $user->name,
                'performance_guide_id' => $guide->id,
                'assessee_no_id' => $input['assessee_no_id'],
                'assessee_school' => $input['assessee_school'],
            ]);

            foreach ($input['available'] as $datetime) {
                $assessment->schedules()->create([
                    'proposed_date' => $datetime,
                ]);
            }

            /** @var User $assessor */
            $assessor = $assessment->assessor;
            $assessor->notify(new AssessorScheduleSubmission($user, $assessment));

            return back()->with('success', __('action.created', ['menu' => __('menu.assessment')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Request $request, Assessment $assessment): RedirectResponse
    {
        try {
            $userId = $request->user()->id;
            if ($assessment->assessee_id !== $userId)
                abort(403);

            if ($assessment->enumStatus() !== AssessmentStatus::Pending)
                abort(403);

            $assessment->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.assessment')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return back()->with('error', $exception->getMessage());
        }
    }
    public function print(Assessment $assessment): View
    {
        return view('pdf.assessee-assessment', compact('assessment'));
    }
}
