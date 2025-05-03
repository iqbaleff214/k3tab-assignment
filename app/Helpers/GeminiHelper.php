<?php

namespace App\Helpers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GeminiHelper
{
    private static string $baseUrl = 'https://generativelanguage.googleapis.com';

    private static function url(string $endpoint): string
    {
        return sprintf('%s/%s?key=%s', self::$baseUrl, $endpoint, config('gemini.api_key'));
    }

    public static function isAvailable(): bool
    {
        return config('gemini.is_available');
    }

    /**
     * Generate text from text-only input
     * [POST] /v1beta/models/gemini-1.5-flash:generateContent
     * https://ai.google.dev/gemini-api/docs/text-generation?lang=rest#generate-text-from-text
     *
     * @throws ConnectionException
     */
    public static function generateTextFromTextOnlyInput(string $text): array
    {
        $endpoint = 'v1beta/models/gemini-1.5-flash:generateContent';
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $text],
                    ],
                ],
            ],
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Generate text from text and image input
     * [POST] /v1beta/models/gemini-1.5-flash:generateContent
     * https://ai.google.dev/gemini-api/docs/text-generation?lang=rest#generate-text-from-text-and-image
     *
     * @throws ConnectionException
     */
    public static function generateTextFromTextAndImageInput(string $text, string $mime, string $base64): array
    {
        $endpoint = 'v1beta/models/gemini-1.5-flash:generateContent';
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $text],
                        [
                            'inline_data' => [
                                'mime_type' => $mime,
                                'data' => $base64,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return json_decode($response->body(), true);
    }
}
