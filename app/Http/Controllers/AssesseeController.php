<?php

namespace App\Http\Controllers;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Enum\UserType;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Mail\AccountCreatedMail;
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

class AssesseeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('assessee/Index', [
            'items' => User::query()
                ->where('type', UserType::Assessee)
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
            $assessee = User::query()
                ->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($password),
                    'phone' => $input['phone'],
                    'type' => UserType::Assessee,
                ]);
            Mail::to($input['email'])
                ->send(new AccountCreatedMail($assessee, $password));

            return back()->with('success', __('action.created', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $assessee): Response
    {
        // Load assessee with related data
        $assessee->load(['roles']);

        // Assessment Statistics
        $totalAssessments = $assessee->assessments()->count();
        $completedAssessments = $assessee->assessments()
            ->where('status', AssessmentStatus::Completed->value)
            ->count();
        $scheduledAssessments = $assessee->assessments()
            ->where('status', AssessmentStatus::Scheduled->value)
            ->count();
        $pendingAssessments = $assessee->assessments()
            ->where('status', AssessmentStatus::Pending->value)
            ->count();

        // Module Statistics
        $enrolledModules = $assessee->moduleAssessees()->with('module')->get();
        $totalModules = $enrolledModules->count();

        // Post-Test Statistics
        $postTests = \App\Models\PostTest::where('user_id', $assessee->id)->get();
        $completedPostTests = $postTests->whereNotNull('answers')->count();
        $passedPostTests = $postTests->where('is_passed', true)->count();
        $totalPostTests = $postTests->count();

        // Assessment History with Performance Guides
        $assessmentHistory = $assessee->assessments()
            ->with(['assessor', 'guide', 'schedules'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'assessor_name' => $assessment->assessor->name ?? 'N/A',
                    'guide_name' => $assessment->guide->skill_number ?? 'N/A',
                    'status' => $assessment->status,
                    'result' => $assessment->result,
                    'comment' => $assessment->comment,
                    'scheduled_at' => $assessment->scheduled_at?->format('Y-m-d H:i'),
                    'finished_at' => $assessment->finished_at?->format('Y-m-d H:i'),
                    'created_at' => $assessment->created_at->diffForHumans(),
                    'created_at_formatted' => $assessment->created_at->format('M j, Y'),
                ];
            });

        // Module Progress Details
        $moduleProgress = $enrolledModules->map(function ($moduleAssessee) use ($assessee) {
            $module = $moduleAssessee->module;
            $postTest = \App\Models\PostTest::where('module_id', $module->id)
                ->where('user_id', $assessee->id)
                ->first();

            return [
                'id' => $module->id,
                'title' => $module->title,
                'code' => $module->code,
                'enrolled_at' => $moduleAssessee->created_at->format('M j, Y'),
                'post_test_completed' => $postTest !== null && $postTest->answers !== null,
                'post_test_passed' => $postTest?->is_passed ?? false,
                'post_test_score' => $postTest?->score ?? 0,
                'minimum_score' => $postTest?->minimum_score ?? $module->minimum_score ?? 0,
                'questions_available' => $module->questions->count(),
            ];
        })->sortByDesc('enrolled_at')->values();

        // Performance Timeline (Last 6 months)
        $performanceTimeline = $assessee->assessments()
            ->select([
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('COUNT(*) as assessment_count'),
                \DB::raw('COUNT(CASE WHEN status = \'' . AssessmentStatus::Completed->value . '\' THEN 1 END) as completed_count')
            ])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->orderBy(\DB::raw('DATE(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => date('M j', strtotime($item->date)),
                    'assessment_count' => $item->assessment_count,
                    'completed_count' => $item->completed_count,
                ];
            });

        // Skills/Performance Guide Statistics
        $skillsStatistics = $assessee->assessments()
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
            ->sortByDesc('success_rate')
            ->values()
            ->take(5);

        // Recent Activity
        $recentActivity = collect()
            ->merge($assessee->assessments()->latest()->limit(3)->get()->map(function ($assessment) {
                return [
                    'type' => 'assessment',
                    'title' => 'Assessment: ' . ($assessment->guide->skill_number ?? 'N/A'),
                    'description' => 'Status: ' . ucfirst($assessment->status),
                    'date' => $assessment->created_at->diffForHumans(),
                    'icon' => 'clipboard-check',
                ];
            }))
            ->merge($postTests->sortByDesc('created_at')->take(3)->map(function ($postTest) {
                return [
                    'type' => 'post_test',
                    'title' => 'Post-Test: ' . ($postTest->module->title ?? 'N/A'),
                    'description' => $postTest->is_passed ? 'Passed' : 'Completed',
                    'date' => $postTest->created_at->diffForHumans(),
                    'icon' => 'academic-cap',
                ];
            }))
            ->sortByDesc('date')
            ->take(5)
            ->values();

        return Inertia::render('assessee/Show', [
            'item' => $assessee,
            'next' => User::query()->where('type', UserType::Assessee)
                ->where('id', '>', $assessee->id)
                ->value('id'),
            'prev' => User::query()->where('type', UserType::Assessee)
                ->where('id', '<', $assessee->id)
                ->orderByDesc('created_at')->value('id'),
            'total' => User::query()->where('type', UserType::Assessee)->count(),
            'index' => User::query()->where('type', UserType::Assessee)
                    ->where('id', '<', $assessee->id)->count('id') + 1,
            'stats' => [
                'assessments' => [
                    'total' => $totalAssessments,
                    'completed' => $completedAssessments,
                    'scheduled' => $scheduledAssessments,
                    'pending' => $pendingAssessments,
                    'completion_rate' => $totalAssessments > 0 ? round(($completedAssessments / $totalAssessments) * 100, 1) : 0,
                ],
                'modules' => [
                    'total' => $totalModules,
                    'post_tests_completed' => $completedPostTests,
                    'post_tests_passed' => $passedPostTests,
                    'post_test_completion_rate' => $totalPostTests > 0 ? round(($completedPostTests / $totalPostTests) * 100, 1) : 0,
                    'post_test_success_rate' => $completedPostTests > 0 ? round(($passedPostTests / $completedPostTests) * 100, 1) : 0,
                ],
            ],
            'charts' => [
                'performance_timeline' => $performanceTimeline,
                'skills_statistics' => $skillsStatistics,
            ],
            'assessment_history' => $assessmentHistory,
            'module_progress' => $moduleProgress,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $assessee): RedirectResponse
    {
        try {
            $input = $request->validated();

            $assessee->update([
                'name' => $input['name'],
                'email' => $input['email'] ?? $assessee->email,
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
    public function destroy(Request $request, User $assessee): RedirectResponse
    {
        try {
            $assessee->delete();

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
                ->where('type', UserType::Assessee)
                ->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.user')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
