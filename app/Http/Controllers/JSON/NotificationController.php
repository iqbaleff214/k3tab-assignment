<?php

namespace App\Http\Controllers\JSON;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => $request->user()
                    ->notifications()
                    ->paginate($request->query('size', 50))
                    ->withQueryString(),
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
