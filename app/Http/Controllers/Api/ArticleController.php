<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'source'   => 'nullable|in:newsapi,guardian,nyt',
            'category' => 'nullable|string',
            'date'     => 'nullable|date_format:Y-m-d',
            'search'   => 'nullable|string|max:100',
            'limit'    => 'nullable|integer|min:1|max:100',
            'offset'   => 'nullable|integer|min:0',
        ]);

        $q = Article::query()
            ->fromSource($validated['source'] ?? null)
            ->category($validated['category'] ?? null)
            ->onDate($validated['date'] ?? null)
            ->search($validated['search'] ?? null)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('limit') || $request->filled('offset')) {
            $limit  = (int)($validated['limit']  ?? 20);
            $offset = (int)($validated['offset'] ?? 0);

            $total = (clone $q)->count();
            $items = (clone $q)->skip($offset)->take($limit)->get();

            return ArticleResource::collection($items)->additional([
                'meta' => [
                    'total'       => $total,
                    'limit'       => $limit,
                    'offset'      => $offset,
                    'next_offset' => ($offset + $limit) < $total ? $offset + $limit : null,
                    'prev_offset' => max($offset - $limit, 0),
                    'has_more'    => ($offset + $limit) < $total,
                ],
            ]);
        }

        // Default set page-based pagination
        $articles = $q->paginate(15);
        return ArticleResource::collection($articles);
    }
}