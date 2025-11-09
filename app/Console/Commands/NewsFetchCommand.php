<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchProviderArticlesJob;

class NewsFetchCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {provider? : newsapi|guardian|nyt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest articles from providers';

    public function handle(): int
    {
        $map = [
            'newsapi' => \App\Services\News\NewsApiService::class,
            'guardian'=> \App\Services\News\GuardianService::class,
            'nyt'     => \App\Services\News\NytService::class,
        ];

        if ($p = $this->argument('provider')) {
            throw_if(!isset($map[$p]), \InvalidArgumentException::class, 'Unknown provider');
            FetchProviderArticlesJob::dispatch($map[$p]);
            $this->info("Queued fetch for {$p}");
            return self::SUCCESS;
        }

        // All providers
        foreach ($map as $class) {
            FetchProviderArticlesJob::dispatch($class);
        }
        $this->info('Queued fetch for all providers');
        return self::SUCCESS;
    }
}
