<?php

namespace App\Http\Controllers\JSON;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $roles = Role::query()
                ->when($request->query('keyword'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })->paginate($request->query('size', 25));
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'data' => $roles,
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
