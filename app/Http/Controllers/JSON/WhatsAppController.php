<?php

namespace App\Http\Controllers\JSON;

use App\Helpers\WhatsAppHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        try {
            $token = $request->input('token');
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), $token);

            $response = WhatsAppHelper::sessionStatus($headers);
            Log::debug('response', $response);
            if (!isset($response['data'])) {
                throw new \Exception('Something went wrong');
            }

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => $response['data'],
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

    public function qr(Request $request): JsonResponse
    {
        try {
            $token = $request->input('token');

            $headers = WhatsAppHelper::headers(config('whatsapp.token'), $token);
            Log::debug('headers', $headers);
            $response = WhatsAppHelper::sessionQR($headers);
            Log::debug('response', $response);
            if (!isset($response['data'])) {
                throw new \Exception('Something went wrong');
            }

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => $response['data']['QRCode'],
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
