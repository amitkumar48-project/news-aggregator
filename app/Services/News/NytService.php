<?php

namespace App\Services\News;

use App\Domain\News\Contracts\NewsProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NytService implements NewsProvider
{
    public function fetchLatest(): Collection
    {
        $key = config('services.nyt.key');

        // Fetching multiple sections to increase the volume
        $sections = ['home','world','science','technology','business'];
        $all = collect();

        foreach ($sections as $section) {
            $json = Http::get(
                "https://api.nytimes.com/svc/topstories/v2/{$section}.json",
                ['api-key' => $key]
            )->throw()->json();

            $items = collect($json['results'] ?? [])->map(function ($a) use ($section) {
                // Picking the best available image across common NYT formats
                $media = collect($a['multimedia'] ?? []);
                $preferredFormats = [
                    'superJumbo',
                    'threeByTwoLargeAt2X',
                    'threeByTwoMediumAt2X',
                    'threeByTwoSmallAt2X',
                    'thumbLarge',
                    'Standard Thumbnail',
                ];

                $thumb = null;
                foreach ($preferredFormats as $fmt) {
                    $thumb = optional($media->firstWhere('format', $fmt))['url'] ?? null;
                    if ($thumb) break;
                }
                // Fallback: first multimedia URL if any
                if (!$thumb) {
                    $thumb = $media->first()['url'] ?? null;
                }

                return [
                    'source'       => 'nyt',
                    'external_id'  => (string) data_get($a, 'uri'),            
                    'title'        => (string) data_get($a, 'title'),
                    'description'  => data_get($a, 'abstract'),
                    'author'       => data_get($a, 'byline'),
                    'category'     => data_get($a, 'section') ?: $section,
                    'published_at' => data_get($a, 'published_date'),
                    'url'          => (string) data_get($a, 'url'),
                    'thumbnail'    => $thumb,                                   
                    'content'      => data_get($a, 'abstract'),                 
                    'source_raw'   => $a,
                ];
            });

            $all = $all->concat($items);
        }

        return $all->unique('url')->values();
    }
}
