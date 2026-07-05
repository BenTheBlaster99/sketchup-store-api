<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoTagService
{
    public function suggestTags(string $thumbnailUrl): array
    {
        $apiKey = (string) env('ANTHROPIC_API_KEY');

        if ($apiKey === '') {
            Log::info('AutoTag skipped: ANTHROPIC_API_KEY is not configured.');

            return [];
        }

        try {
            $availableTags = Tag::orderBy('group')
                ->orderBy('name')
                ->get()
                ->groupBy('group')
                ->map(fn ($tags) => $tags->pluck('name')->values())
                ->toArray();

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-latest'),
                'max_tokens' => 512,
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'url',
                                'url' => $thumbnailUrl,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Analyze this 3D furniture model image. Available tags by group: '
                                .json_encode($availableTags)
                                .'. Return ONLY valid JSON, no explanation: {"suggested_tags":["exact tag name 1","exact tag name 2"]}. Only suggest tags from the available list. Max 5 tags.',
                        ],
                    ],
                ]],
            ]);

            if (! $response->successful()) {
                Log::warning('AutoTag failed response: '.$response->body());

                return [];
            }

            $content = $response->json('content.0.text', '{}');
            $parsed = json_decode(trim((string) $content), true);

            if (! is_array($parsed) || ! isset($parsed['suggested_tags']) || ! is_array($parsed['suggested_tags'])) {
                return [];
            }

            return Tag::whereIn('name', $parsed['suggested_tags'])
                ->pluck('id')
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('AutoTag failed: '.$e->getMessage());

            return [];
        }
    }
}
