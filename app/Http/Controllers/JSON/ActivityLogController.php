<?php

namespace App\Http\Controllers\JSON;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function index(Request $request, string $module, $id): JsonResponse
    {
        try {
            $logName = 'App\Models\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => ActivityLog::with(['causer'])
                    ->when($id, function ($query) use ($id, $module) {
                        return $query->when($module !== 'user', function ($query) use ($id) {
                            return $query->where('subject_id', $id);
                        }, function ($query) use ($id) {
                            return $query->where('causer_id', $id);
                        });
                    })->where('log_name', $logName)->orderByDesc('id')
                    ->cursorPaginate($request->query('size', 25)),
            ]);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
