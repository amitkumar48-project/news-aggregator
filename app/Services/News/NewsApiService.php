<?php

namespace App\Services\News;

use App\Domain\News\Contracts\NewsProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class NewsApiService implements NewsProvider
{
     public function fetchLatest(): Collection
    {
        $key = config('services.newsapi.key');
        if (empty($key)) {
            throw new InvalidArgumentException('NEWSAPI_KEY is missing. Set it in .env and run php artisan optimize:clear');
        }

        $country = config('services.newsapi.country', 'us');
        $categories = ['business','entertainment','general','health','science','sports','technology'];

        $all = collect();

        foreach ($categories as $cat) {
            $json = Http::retry(2, 200)->get('https://newsapi.org/v2/top-headlines', [
                'apiKey'   => $key,      
                'country'  => $country,
                'category' => $cat,
                'pageSize' => 100,
                'language' => 'en',
            ])->throw()->json();

            $items = collect($json['articles'] ?? [])->map(function ($a) use ($cat) {
                $u = (string) data_get($a, 'url');
                return [
                    'source'       => 'newsapi',
                    'external_id' => sha1($u ?: ((data_get($a,'title')??'').'|'.(data_get($a,'publishedAt')??''))),
                    'title'        => (string) data_get($a, 'title'),
                    'description'  => data_get($a, 'description'),
                    'author'       => data_get($a, 'author'),
                    'category'     => $cat ?? null,  
                    'published_at' => data_get($a, 'publishedAt'),
                    'url'         => $u,
                    'thumbnail'    => data_get($a, 'urlToImage'),
                    'content'      => data_get($a, 'content'),
                    'source_raw'   => $a,
                ];
            });

            $all = $all->concat($items);
        }

        return $all->unique('url')->values();
    }
}
