<?php

namespace App\Http\Controllers\API\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        try {
            Log::debug('whatsapp', $request->all());

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'data' => $request->input(),
            ], 500);
        }
    }
}
