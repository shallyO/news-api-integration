<?php

namespace App\Mappers;

use Carbon\Carbon;

class NewYorkTimesArticleMapper
{
    /**
     * Map New York Times API response to a structured format for saving articles.
     *
     * @param array $response
     * @param string|null $category
     * @return array
     */
    public static function mapResponseToSchema(array $response, ?string $category = null): array
    {
        if (!isset($response['response']['docs']) || !is_array($response['response']['docs'])) {
            return [];
        }

        return array_map(function ($article) use ($category) {
            return [
                'source' => [
                    'name' => 'New York Times', // Static source name since NYT API does not provide a dynamic one
                ],
                'author' => $article['byline']['original'] ?? null, // NYT provides author info in 'byline'
                'title' => $article['headline']['main'] ?? null,
                'description' => $article['snippet'] ?? null,
                'url' => $article['web_url'] ?? null,
                'urlToImage' => isset($article['multimedia'][0]) ? 'https://static01.nyt.com/' . $article['multimedia'][0]['url'] : null, // Building image URL from multimedia data
                'publishedAt' => isset($article['pub_date']) ? Carbon::parse($article['pub_date'])->format('Y-m-d H:i:s') : null,
                'content' => $article['lead_paragraph'] ?? null,
                'category' => $category ?? $article['section_name'], // Using section name as category if not provided
            ];
        }, $response['response']['docs']);
    }
}
