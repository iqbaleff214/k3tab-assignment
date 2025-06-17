<?php

namespace App\Http\Controllers\Configuration;

use App\Helpers\WhatsAppHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsApp\StoreRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class WhatsAppController extends Controller
{
    public function index(Request $request): Response
    {
        $availability = true;
        try {
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), null);
            $items = WhatsAppHelper::adminUsers($headers);
        } catch (ConnectionException $e) {
            $availability = false;
            $items = [];
        }

        return Inertia::render('configuration/WhatsApp', [
            'items' => $items['data'] ?? [],
            'availability' => $availability,
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $input = $request->validated();
            $input['token'] = Str::random(32);
            $input['webhook'] = route('webhook.whatsapp');

            $headers = WhatsAppHelper::headers(config('whatsapp.token'), null);
            $response = WhatsAppHelper::createAdminUser($input, $headers);
            if (!isset($response['data'])) {
                throw new \Exception('Something went wrong');
            }

            $device = $response['data'];
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), $device['token']);
            $response = WhatsAppHelper::sessionConnect([$input['events']], $headers);
            if (!isset($response['data'])) {
                throw new \Exception('Something went wrong');
            }

            return back()->with('success', __('action.created', ['menu' => __('menu.whatsapp')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        try {
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), null);
            $response = WhatsAppHelper::destroyAdminUser($id, true, $headers);
            if (!$response['success']) {
                throw new \Exception($response['details'] ?? 'failed');
            }

            return back()->with('success', __('action.deleted', ['menu' => __('menu.whatsapp')]));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', $exception->getMessage());
        }
    }
}
