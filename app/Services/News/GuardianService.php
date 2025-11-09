<?php

namespace App\Services\News;

use App\Domain\News\Contracts\NewsProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class GuardianService implements NewsProvider
{
    public function fetchLatest(): Collection
    {
        $key = config('services.guardian.key');

        if (empty($key)) {
            throw new InvalidArgumentException('GUARDIAN_KEY is missing. Set it in .env and run php artisan optimize:clear');
        }

        $response = Http::retry(2, 200)
            ->get('https://content.guardianapis.com/search', [
                'api-key'     => $key,
                'show-fields' => 'trailText,thumbnail,byline,body',
                'page-size'   => 100,
                'order-by'    => 'newest',
            ]);

        if ($response->unauthorized()) {
            Log::warning('Guardian unauthorized', [
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Guardian API unauthorized. Double-check GUARDIAN_KEY or create a new key.');
        }

        $json = $response->throw()->json();

        return collect(data_get($json, 'response.results', []))->map(function ($a) {
            return [
                'source'       => 'guardian',
                'external_id'  => (string) data_get($a, 'id'),
                'title'        => (string) data_get($a, 'webTitle'),
                'description'  => data_get($a, 'fields.trailText'),
                'author'       => data_get($a, 'fields.byline'),
                'category'     => data_get($a, 'sectionId'),
                'published_at' => data_get($a, 'webPublicationDate'),
                'url'          => (string) data_get($a, 'webUrl'),
                'thumbnail'    => data_get($a, 'fields.thumbnail'),
                'content'      => data_get($a, 'fields.body'),
                'source_raw'   => $a,
            ];
        });
    }
}
