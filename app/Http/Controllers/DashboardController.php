<?php

namespace App\Http\Controllers;

use App\Enum\AssessmentStatus;
use App\Enum\UserType;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\PostTest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Summary Statistics
        $totalUsers = User::count();
        $totalAssessors = User::where('type', UserType::Assessor->value)->count();
        $totalAssessees = User::where('type', UserType::Assessee->value)->count();
        $totalModules = Module::count();
        $totalAssessments = Assessment::count();

        // Assessment Statistics
        $pendingAssessments = Assessment::where('status', AssessmentStatus::Pending->value)->count();
        $completedAssessments = Assessment::where('status', AssessmentStatus::Completed->value)->count();
        $inProgressAssessments = Assessment::where('status', AssessmentStatus::Scheduled->value)->count();

        // Module Statistics
        $modulesWithPostTests = Module::whereHas('questions')->count();
        $totalPostTests = PostTest::count();
        $completedPostTests = PostTest::whereNotNull('answers')->count();

        // Recent Activity
        $recentAssessments = Assessment::with(['assessee', 'assessor', 'guide'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'assessee_name' => $assessment->assessee_name ?? $assessment->assessee->name ?? 'N/A',
                    'assessor_name' => $assessment->assessor->name ?? 'N/A',
                    'guide_name' => $assessment->guide->skill_number ?? 'N/A',
                    'status' => $assessment->status,
                    'scheduled_at' => $assessment->scheduled_at?->format('Y-m-d H:i'),
                    'created_at' => $assessment->created_at->diffForHumans(),
                ];
            });

        // Assessment Status Distribution for Chart
        $assessmentStatusChart = Assessment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Monthly Assessment Trend (Last 6 months)
        $monthlyTrend = Assessment::select(
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'count' => $item->count,
                ];
            });

        // Top Performance Guides Usage
        $topGuides = Assessment::select('performance_guide_id', DB::raw('COUNT(*) as usage_count'))
            ->with('guide:id,skill_number')
            ->whereNotNull('performance_guide_id')
            ->groupBy('performance_guide_id')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->guide->skill_number ?? 'N/A',
                    'usage_count' => $item->usage_count,
                ];
            });

        // User Registration Trend (Last 30 days)
        $userRegistrationTrend = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => date('M j', strtotime($item->date)),
                    'count' => $item->count,
                ];
            });

        // Module Completion Rate
        $moduleStats = Module::with(['postTests' => function ($query) {
            $query->whereNotNull('answers');
        }, 'assessees'])
            ->get()
            ->map(function ($module) {
                $totalEnrolled = $module->assessees->count();
                $completed = $module->postTests->count();
                $completionRate = $totalEnrolled > 0 ? round(($completed / $totalEnrolled) * 100, 1) : 0;

                return [
                    'title' => $module->title,
                    'enrolled' => $totalEnrolled,
                    'completed' => $completed,
                    'completion_rate' => $completionRate,
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(5)
            ->values();

        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => [
                    'total' => $totalUsers,
                    'assessors' => $totalAssessors,
                    'assessees' => $totalAssessees,
                ],
                'modules' => [
                    'total' => $totalModules,
                    'with_tests' => $modulesWithPostTests,
                ],
                'assessments' => [
                    'total' => $totalAssessments,
                    'pending' => $pendingAssessments,
                    'in_progress' => $inProgressAssessments,
                    'completed' => $completedAssessments,
                ],
                'post_tests' => [
                    'total' => $totalPostTests,
                    'completed' => $completedPostTests,
                ],
            ],
            'charts' => [
                'assessment_status' => $assessmentStatusChart,
                'monthly_trend' => $monthlyTrend,
                'user_registration' => $userRegistrationTrend,
                'top_guides' => $topGuides,
            ],
            'recent_assessments' => $recentAssessments,
            'module_stats' => $moduleStats,
        ]);
    }
}
