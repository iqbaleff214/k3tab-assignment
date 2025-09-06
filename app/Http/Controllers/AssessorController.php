<?php

namespace App\Http\Controllers;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Enum\UserType;
use App\Events\User\NewUserCreated;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Mail\AccountCreatedMail;
use App\Models\Assessment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AssessorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('assessor/Index', [
            'items' => User::query()
                ->where('type', UserType::Assessor)
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

            $password = Str::random(8);
            $assessor = User::query()
                ->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($password),
                    'phone' => $input['phone'],
                    'type' => UserType::Assessor,
                ]);
            Mail::to($input['email'])
                ->send(new AccountCreatedMail($assessor, $password));

            return back()->with('success', __('action.created', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $assessor): Response
    {
        // Load assessor with related data
        $assessor->load(['roles']);
        
        // Assessment Statistics as Assessor
        $totalAssessments = $assessor->assessorAssessments()->count();
        $completedAssessments = $assessor->assessorAssessments()
            ->where('status', AssessmentStatus::Completed->value)
            ->count();
        $scheduledAssessments = $assessor->assessorAssessments()
            ->where('status', AssessmentStatus::Scheduled->value)
            ->count();
        $pendingAssessments = $assessor->assessorAssessments()
            ->where('status', AssessmentStatus::Pending->value)
            ->count();

        // Assessment Results Statistics
        $passedAssessments = $assessor->assessorAssessments()
            ->where('result', AssessmentResult::Competent->value)
            ->count();
        $failedAssessments = $assessor->assessorAssessments()
            ->where('result', AssessmentResult::NotCompetent->value)
            ->count();

        // Workload and Performance Metrics
        $assesseesSupervised = $assessor->assessorAssessments()
            ->distinct('assessee_id')
            ->count('assessee_id');
        
        $averageAssessmentsPerMonth = $assessor->assessorAssessments()
            ->where('created_at', '>=', now()->subMonths(6))
            ->count() / 6;

        // Recent Assessments with Details
        $recentAssessments = $assessor->assessorAssessments()
            ->with(['assessee', 'guide'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'assessee_name' => $assessment->assessee_name ?? $assessment->assessee->name ?? 'N/A',
                    'guide_name' => $assessment->guide->skill_number ?? 'N/A',
                    'status' => $assessment->status,
                    'result' => $assessment->result,
                    'scheduled_at' => $assessment->scheduled_at?->format('Y-m-d H:i'),
                    'finished_at' => $assessment->finished_at?->format('Y-m-d H:i'),
                    'created_at' => $assessment->created_at->diffForHumans(),
                    'created_at_formatted' => $assessment->created_at->format('M j, Y'),
                ];
            });

        // Monthly Assessment Trend (Last 6 months)
        $assessmentTrend = $assessor->assessorAssessments()
            ->select([
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total_assessments'),
                DB::raw('COUNT(CASE WHEN status = \'' . AssessmentStatus::Completed->value . '\' THEN 1 END) as completed_count'),
                DB::raw('COUNT(CASE WHEN result = \'' . AssessmentResult::Competent->value . '\' THEN 1 END) as passed_count')
            ])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'total_assessments' => $item->total_assessments,
                    'completed_count' => $item->completed_count,
                    'passed_count' => $item->passed_count,
                    'success_rate' => $item->completed_count > 0 ? round(($item->passed_count / $item->completed_count) * 100, 1) : 0,
                ];
            });

        // Performance Guide Usage Statistics
        $skillsExpertise = $assessor->assessorAssessments()
            ->with('guide')
            ->whereNotNull('performance_guide_id')
            ->where('status', AssessmentStatus::Completed->value)
            ->get()
            ->groupBy('performance_guide_id')
            ->map(function ($assessments) {
                $guide = $assessments->first()->guide;
                $passedCount = $assessments->where('result', AssessmentResult::Competent->value)->count();
                $totalCount = $assessments->count();
                
                return [
                    'skill_name' => $guide->skill_number ?? 'N/A',
                    'total_assessments' => $totalCount,
                    'passed_assessments' => $passedCount,
                    'success_rate' => $totalCount > 0 ? round(($passedCount / $totalCount) * 100, 1) : 0,
                    'last_assessment' => $assessments->sortByDesc('created_at')->first()->created_at->format('M j, Y'),
                ];
            })
            ->sortByDesc('total_assessments')
            ->values()
            ->take(10);

        // Efficiency Metrics
        $avgTimeToComplete = $assessor->assessorAssessments()
            ->whereNotNull('finished_at')
            ->whereNotNull('scheduled_at')
            ->get()
            ->map(function ($assessment) {
                return $assessment->scheduled_at->diffInHours($assessment->finished_at);
            })
            ->avg();

        // Student Success Rate by Assessor
        $studentSuccessRate = $completedAssessments > 0 ? 
            round(($passedAssessments / $completedAssessments) * 100, 1) : 0;

        // Recent Activity Feed
        $recentActivity = collect()
            ->merge($assessor->assessorAssessments()->latest()->limit(5)->get()->map(function ($assessment) {
                $statusText = match($assessment->status) {
                    'pending' => 'Created new assessment',
                    'scheduled' => 'Scheduled assessment',
                    'completed' => 'Completed assessment',
                    'cancelled' => 'Cancelled assessment',
                    default => 'Updated assessment'
                };
                
                return [
                    'type' => 'assessment',
                    'title' => $statusText,
                    'description' => 'Student: ' . ($assessment->assessee_name ?? 'N/A') . ' • Skill: ' . ($assessment->guide->skill_number ?? 'N/A'),
                    'date' => $assessment->created_at->diffForHumans(),
                    'icon' => 'clipboard-check',
                    'status' => $assessment->status,
                ];
            }))
            ->sortByDesc('date')
            ->take(8)
            ->values();

        // Assessment Distribution by Day of Week
        $weeklyDistribution = $assessor->assessorAssessments()
            ->where('created_at', '>=', now()->subMonths(3))
            ->get()
            ->groupBy(function ($assessment) {
                return $assessment->created_at->dayOfWeek;
            })
            ->map(function ($assessments, $dayOfWeek) {
                return [
                    'day' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$dayOfWeek],
                    'count' => $assessments->count(),
                ];
            })
            ->sortBy(function ($item, $key) {
                return $key; // Sort by day of week
            })
            ->values();

        return Inertia::render('assessor/Show', [
            'item' => $assessor,
            'next' => User::query()->where('type', UserType::Assessor)
                ->where('id', '>', $assessor->id)
                ->value('id'),
            'prev' => User::query()->where('type', UserType::Assessor)
                ->where('id', '<', $assessor->id)
                ->orderByDesc('created_at')->value('id'),
            'total' => User::query()->where('type', UserType::Assessor)->count(),
            'index' => User::query()->where('type', UserType::Assessor)
                ->where('id', '<', $assessor->id)->count('id') + 1,
            'stats' => [
                'assessments' => [
                    'total' => $totalAssessments,
                    'completed' => $completedAssessments,
                    'scheduled' => $scheduledAssessments,
                    'pending' => $pendingAssessments,
                    'completion_rate' => $totalAssessments > 0 ? round(($completedAssessments / $totalAssessments) * 100, 1) : 0,
                ],
                'results' => [
                    'passed' => $passedAssessments,
                    'failed' => $failedAssessments,
                    'success_rate' => $studentSuccessRate,
                ],
                'workload' => [
                    'assessees_supervised' => $assesseesSupervised,
                    'avg_assessments_per_month' => round($averageAssessmentsPerMonth, 1),
                    'avg_time_to_complete' => $avgTimeToComplete ? round($avgTimeToComplete, 1) : 0,
                ],
            ],
            'charts' => [
                'assessment_trend' => $assessmentTrend,
                'skills_expertise' => $skillsExpertise,
                'weekly_distribution' => $weeklyDistribution,
            ],
            'recent_assessments' => $recentAssessments,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $assessor): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessor->update([
                'name' => $input['name'],
                'email' => $input['email'] ?? $assessor->email,
                'phone' => $input['phone'],
            ]);

            return back()->with('success', __('action.updated', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $assessor): RedirectResponse
    {
        try {
            $assessor->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.user')]));
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

            User::query()
                ->whereIn('id', $ids)
                ->where('type', UserType::Assessor)
                ->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
