<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    /**
     * Fetch articles from the database based on filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchArticles(array $filters)
    {
        // Build the query with the given filters
        $query = Article::query();

        if (!empty($filters['query'])) {
            $query->where('title', 'LIKE', '%' . $filters['query'] . '%');
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['authors'])) {
            $query->whereIn('author', $filters['authors']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', $filters['date_to']);
        }

        // Fetch and return articles from the database
        return $query->get();
    }

    /**
     * Save articles to the database
     *
     * @param array $articles
     * @return void
     */
    public function saveArticles(array $articles)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['url' => $article['url']], // Ensure no duplicate articles by URL
                [
                    'source_id' => $article['source']['id'] ?? null,
                    'source_name' => $article['source']['name'] ?? null,
                    'author' => $article['author'] ?? null,
                    'title' => $article['title'],
                    'description' => $article['description'] ?? null,
                    'url_to_image' => $article['urlToImage'] ?? null,
                    'published_at' => $article['publishedAt'] ?? null,
                    'content' => $article['content'] ?? null,
                    'category' => $article['category'] ?? null,
                ]
            );
        }
    }
}
