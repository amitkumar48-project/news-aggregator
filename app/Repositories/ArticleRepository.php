<?php

namespace App\Repositories;
use App\Models\Article;
use Illuminate\Support\Arr;

class ArticleRepository
{
    public function upsertMany(array $normalizedArticles): void
    {
        foreach ($normalizedArticles as $data) {
            $payload = Arr::only($data, [
                'source','external_id','title','description','author','category',
                'published_at','url','thumbnail','content','source_raw'
            ]);

            // 1) I have tries by URL first (canonical)
            $existing = null;
            if (!empty($payload['url'])) {
                $existing = Article::query()->where('url', $payload['url'])->first();
            }

            // 2) If we not found by URL, then I tried by (source, external_id)
            if (!$existing && !empty($payload['external_id'])) {
                $existing = Article::query()
                    ->where('source', $payload['source'])
                    ->where('external_id', $payload['external_id'])
                    ->first();
            }

            if ($existing) {
                $existing->fill($payload)->save();
            } else {
                Article::create($payload);
            }
        }
    }
}
