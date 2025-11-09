<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'source'       => $this->source,
            'external_id'  => $this->external_id,
            'title'        => $this->title,
            'description'  => $this->description, 
            'author'       => $this->author,
            'category'     => $this->category,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'url'          => $this->url,
            'thumbnail'    => $this->thumbnail,
            'content'      => $this->content,
           // 'source_raw'   => $this->source_raw,            
        ];
    }
}
