<?php

namespace App\Domain\News\Contracts;

use Illuminate\Support\Collection;

interface NewsProvider
{
    /**
     * @return \Illuminate\Support\Collection<array{
     *   source:string, external_id:?string, title:string, description:?string,
     *   author:?string, category:?string, published_at:?string, url:string,
     *   thumbnail:?string, content:?string, source_raw:array
     * }>
     */
    public function fetchLatest(): Collection;
}
