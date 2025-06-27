<?php

namespace App\Http\Controllers\JSON;

use App\Http\Controllers\Controller;
use App\Models\ApprovalFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApprovalFlowController extends Controller
{
    public function show(ApprovalFlow $approval): JsonResponse
    {
        try {
            $approval->load(['children', 'role']);
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => $approval,
            ]);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'data' => $approval,
            ], 500);
        }
    }
}
