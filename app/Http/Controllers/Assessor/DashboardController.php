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
        return Inertia::render('panel/assessor/Dashboard', [
            'assessments' => Assessment::with(['assessee', 'guide', 'schedules'])
                ->where('assessor_id', $user->id)
                ->where('status', AssessmentStatus::Pending)
                ->get(),
        ]);
    }
}
