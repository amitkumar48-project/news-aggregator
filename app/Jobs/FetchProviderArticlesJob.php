<?php

namespace App\Jobs;

use App\Domain\News\Contracts\NewsProvider;
use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchProviderArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $providerClass) {}

    public function handle(ArticleRepository $repo): void
    {
        /** @var NewsProvider $provider */
        $provider = app($this->providerClass);
        $collection = $provider->fetchLatest();
        $repo->upsertMany($collection->all());
    }
}
