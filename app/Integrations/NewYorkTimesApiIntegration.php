<?php

namespace App\Integrations;

use App\Mappers\NewYorkTimesArticleMapper;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class NewYorkTimesApiIntegration
{
    protected $client;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = env('NYTIMES_API_KEY'); 
    }

    /**
     * Fetch articles from New York Times API.
     *
     * @param string|null $query
     * @param string|null $category
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function fetchArticles($query = null, $category = null, $dateFrom = null, $dateTo = null)
    {
        try {
            $queryParams = [
                'api-key' => $this->apiKey,
            ];

            // Add filters to query parameters
            if ($query) {
                $queryParams['q'] = $query;
            }
            if ($category) {
                $queryParams['fq'] = 'section_name:("' . $category . '")';
            }
            if ($dateFrom) {
                $queryParams['begin_date'] = $dateFrom;
            }
            if ($dateTo) {
                $queryParams['end_date'] = $dateTo;
            }

            Log::info('Sending request to New York Times API with parameters:', $queryParams);

            $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
            $response = $this->client->get($url, ['query' => $queryParams]);

            $data = json_decode($response->getBody(), true);

            Log::info('Response from New York Times API:', $data);

            // Use the mapper to transform the response
            return NewYorkTimesArticleMapper::mapResponseToSchema($data, $category);
        } catch (Exception $e) {
            Log::error("Error fetching articles from New York Times API: " . $e->getMessage());
            throw new Exception("Error fetching articles from New York Times API: " . $e->getMessage());
        }
    }

    /**
     * Fetch top news from New York Times API.
     *
     * @param string $section
     * @return array
     */
    public function fetchTopNews(string $section = 'home'): array
    {
        try {
            $url = "https://api.nytimes.com/svc/topstories/v2/{$section}.json";

            $response = $this->client->get($url, [
                'query' => ['api-key' => $this->apiKey]
            ]);

            $data = json_decode($response->getBody(), true);

            Log::info('Response from New York Times Top Stories API:', $data);

            // Use the mapper to transform the response
            return NewYorkTimesArticleMapper::mapResponseToSchema(['response' => ['docs' => $data['results'] ?? []]], null);
        } catch (Exception $e) {
            Log::error("Error fetching top news from New York Times API: " . $e->getMessage());
            throw new Exception("Error fetching top news from New York Times API: " . $e->getMessage());
        }
    }
}
