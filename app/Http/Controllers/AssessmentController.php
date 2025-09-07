<?php

namespace App\Http\Controllers;

use App\Enum\UserType;
use App\Models\Assessment;
use App\Models\PerformanceGuide;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('assessment/Index', [
            'items' => Assessment::with(['assessor', 'guide', 'assessee'])
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->render($request->query('size')),
            'assessors' => User::query()
                ->where('type', UserType::Assessor)
                ->get(),
            'assessees' => User::query()
                ->where('type', UserType::Assessee)
                ->get(),
        ]);
    }

    public function destroy(Request $request, Assessment $assessment): RedirectResponse
    {
        try {
            $assessment->delete();

            return back()->with('success', __('action.deleted', ['menu' => __('menu.assessment')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
