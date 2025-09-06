<?php

namespace App\Http\Controllers\Assessee;

use App\Enum\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $userId = $user->id;
        $timezone = $request->input('timezone') ?? config('app.timezone');
        return Inertia::render('panel/assessee/Dashboard', [
            'items' => Assessment::with(['assessor', 'guide', 'guide.module', 'schedules'])
                ->where('assessee_id', $userId)
                ->whereNull('scheduled_at')
                ->get(),
            'events' => Assessment::with(['assessor', 'guide'])
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
                }),
        ]);
    }
}
