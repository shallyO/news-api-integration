<?php

namespace App\Mappers;

use Carbon\Carbon;

class GuardianArticleMapper
{
    /**
     * Map The Guardian API response to a structured format for saving articles.
     *
     * @param array $response
     * @param string|null $category
     * @return array
     */
    public static function mapResponseToSchema(array $response, ?string $category = null): array
    {
        if (!isset($response['response']['results']) || !is_array($response['response']['results'])) {
            return [];
        }

        return array_map(function ($article) use ($category) {
            return [
                'source' => [
                    'name' => 'The Guardian', // Static source name since Guardian API does not provide a dynamic one
                ],
                'author' => null, // Guardian API does not provide an author field
                'title' => $article['webTitle'] ?? null,
                'description' => null, // Guardian API does not provide a description field
                'url' => $article['webUrl'] ?? null,
                'urlToImage' => null, // Guardian API does not provide an image field
                'publishedAt' => isset($article['webPublicationDate']) ? Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s') : null,
                'content' => null, // Guardian API does not provide a content field
                'category' => $category,
            ];
        }, $response['response']['results']);
    }
}
