<?php

namespace App\Http\Controllers\JSON\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $timezone = $request->input('timezone') ?? config('app.timezone');

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => Assessment::with(['assessee', 'guide'])
                    ->whereNotNull('scheduled_at')
                    ->where('assessor_id', $request->input('assessor_id'))
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
                            'title' => $item->guide?->skill_number . ' - ' . $item->assessee_name,
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
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
