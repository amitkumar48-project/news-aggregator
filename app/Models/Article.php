<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'source','external_id','title','description','author',
        'category','published_at','url','thumbnail','content','source_raw'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'source_raw'   => 'array',
    ];

    /* Scopes */
    public function scopeFromSource($q, ?string $source) {
        return $source ? $q->where('source', $source) : $q;
    }
    public function scopeCategory($q, ?string $category) {
        return $category ? $q->where('category', $category) : $q;
    }
    public function scopeOnDate($q, ?string $dateYmd) {
        if (!$dateYmd) return $q;
        return $q->whereDate('published_at', $dateYmd);
    }
    public function scopeSearch($q, ?string $term) {
        if (!$term) return $q;
        return $q->where(function($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
               ->orWhere('description', 'like', "%{$term}%");
        });
    }
}

