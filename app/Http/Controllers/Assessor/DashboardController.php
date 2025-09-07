<?php

namespace App\Http\Controllers\Assessor;

use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        
        // Get pending assessments (existing functionality)
        $pendingAssessments = Assessment::with(['assessee', 'guide', 'schedules'])
            ->where('assessor_id', $user->id)
            ->where('status', AssessmentStatus::Pending)
            ->get();

        // Get dashboard metrics
        $assessmentCounts = Assessment::where('assessor_id', $user->id)
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

        // Get recent completed assessments
        $recentCompleted = Assessment::with(['assessee', 'guide'])
            ->where('assessor_id', $user->id)
            ->where('status', AssessmentStatus::Completed)
            ->orderBy('finished_at', 'desc')
            ->take(5)
            ->get();

        // Get competency results stats
        $competencyStats = Assessment::where('assessor_id', $user->id)
            ->where('status', AssessmentStatus::Completed)
            ->selectRaw('
                COUNT(*) as total_evaluated,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as competent,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as not_competent
            ', [
                \App\Enum\AssessmentResult::Competent->value,
                \App\Enum\AssessmentResult::NotCompetent->value
            ])
            ->first();

        // Get upcoming scheduled assessments
        $upcomingScheduled = Assessment::with(['assessee', 'guide'])
            ->where('assessor_id', $user->id)
            ->where('status', AssessmentStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        return Inertia::render('panel/assessor/Dashboard', [
            'assessments' => $pendingAssessments,
            'metrics' => [
                'counts' => $assessmentCounts,
                'competency' => $competencyStats,
            ],
            'recentCompleted' => $recentCompleted,
            'upcomingScheduled' => $upcomingScheduled,
        ]);
    }
}
