<?php

namespace App\Http\Controllers\Assessee;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\PostTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $userId = $user->id;
        $timezone = $request->input('timezone') ?? config('app.timezone');
        
        // Get pending assessments (existing functionality)
        $pendingAssessments = Assessment::with(['assessor', 'guide', 'guide.module', 'schedules'])
            ->where('assessee_id', $userId)
            ->whereNull('scheduled_at')
            ->get();

        // Get calendar events (existing functionality)
        $events = Assessment::with(['assessor', 'guide'])
            ->whereNotNull('scheduled_at')
            ->where('assessee_id', $userId)
            ->get()->map(function (Assessment $item) use ($timezone) {
                $date = Carbon::createFromFormat("Y-m-d H:i:s", $item->scheduled_at, "UTC")
                    ->setTimezone($timezone)
                    ->format("Y-m-d\TH:i:s");
                $item->load([
                    'guide.module',
                    'guide.module.assessees' => fn ($query) => $query->where('user_id', $item->assessee_id),
                ]);
                return [
                    'id' => $item->id,
                    'title' => $item->guide?->skill_number,
                    'date' => $date,
                    'start' => $date,
                    'end' => Carbon::createFromFormat("Y-m-d H:i:s", $item->scheduled_at, "UTC")
                        ->addHour()
                        ->setTimezone($timezone)
                        ->format("Y-m-d\TH:i:s"),
                    'allDay' => false,
                    'detail' => $item->toArray(),
                    'backgroundColor' => $item->status === 'completed' ? '#ECFDF5' : '#DBEAFE',
                    'borderColor' => $item->status === 'completed' ? '#6EE7B7' : '#93C5FD',
                    'textColor' => $item->status === 'completed' ? '#059669' : '#2563EB',
                ];
            });

        // Dashboard metrics
        $assessmentCounts = Assessment::where('assessee_id', $userId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled
            ', [
                AssessmentStatus::Pending->value,
                AssessmentStatus::Scheduled->value,
                AssessmentStatus::Completed->value,
                AssessmentStatus::Cancelled->value
            ])
            ->first();

        // Module progress - using correct relationship chain through performance guide
        $moduleStats = Module::whereHas('assessees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['guide.assessments' => function ($query) use ($userId) {
                $query->where('assessee_id', $userId);
            }])
            ->with(['postTests' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get()
            ->map(function ($module) {
                $postTest = $module->postTests->first();
                $assessments = $module->guide ? $module->guide->assessments : collect();
                $totalAssessments = $assessments->count();
                $completedAssessments = $assessments->where('status', AssessmentStatus::Completed->value)->count();
                
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'code' => $module->code,
                    'total_assessments' => $totalAssessments,
                    'completed_assessments' => $completedAssessments,
                    'completion_rate' => $totalAssessments > 0 
                        ? round(($completedAssessments / $totalAssessments) * 100, 1) 
                        : 0,
                    'post_test_completed' => $postTest && $postTest->answers !== null,
                    'post_test_score' => $postTest?->score ?? 0,
                    'post_test_passed' => $postTest?->is_passed ?? false,
                ];
            });

        // Assessment results
        $competencyStats = Assessment::where('assessee_id', $userId)
            ->where('status', AssessmentStatus::Completed->value)
            ->selectRaw('
                COUNT(*) as total_evaluated,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as competent,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as not_competent
            ', [
                AssessmentResult::Competent->value,
                AssessmentResult::NotCompetent->value
            ])
            ->first();

        // Recent assessments
        $recentAssessments = Assessment::with(['assessor', 'guide'])
            ->where('assessee_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'assessor_name' => $assessment->assessor?->name ?? 'N/A',
                    'skill_number' => $assessment->guide?->skill_number ?? 'N/A',
                    'status' => $assessment->status,
                    'result' => $assessment->result,
                    'scheduled_at' => $assessment->scheduled_at?->format('M j, Y'),
                    'finished_at' => $assessment->finished_at?->format('M j, Y'),
                    'created_at' => $assessment->created_at->diffForHumans(),
                ];
            });

        // Performance timeline (last 6 months)
        $performanceTimeline = Assessment::where('assessee_id', $userId)
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as assessment_count'),
                DB::raw('COUNT(CASE WHEN status = \'' . AssessmentStatus::Completed->value . '\' THEN 1 END) as completed_count')
            ])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => date('M j', strtotime($item->date)),
                    'assessment_count' => $item->assessment_count,
                    'completed_count' => $item->completed_count,
                ];
            });

        return Inertia::render('panel/assessee/Dashboard', [
            // Existing data
            'items' => $pendingAssessments,
            'events' => $events,
            
            // New metrics and data
            'metrics' => [
                'assessments' => $assessmentCounts,
                'competency' => $competencyStats,
                'modules_enrolled' => $moduleStats->count(),
                'modules_with_post_test' => $moduleStats->where('post_test_completed', true)->count(),
            ],
            'module_progress' => $moduleStats,
            'recent_assessments' => $recentAssessments,
            'performance_timeline' => $performanceTimeline,
        ]);
    }
}
