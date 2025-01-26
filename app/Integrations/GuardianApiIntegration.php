<?php

namespace App\Integrations;

use App\Mappers\GuardianArticleMapper;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class GuardianApiIntegration
{
    protected $client;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = env('GUARDIAN_API_KEY'); // Load the API key from the environment
    }

    /**
     * Fetch articles from The Guardian API.
     *
     * @param string|null $section
     * @param string|null $query
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     * @throws Exception
     */
    public function fetchArticles($section = null, $query = null, $dateFrom = null, $dateTo = null, $category = null)
    {
        try {
            $queryParams = [];
            $q = [];
    
            if ($section) {
                $q[] = is_array($section) ? implode(',', $section) : (string)$section;
            }
            if ($query) {
                $q[] = is_array($query) ? implode(',', $query) : (string)$query;
            }
            if (!empty($q)) {
                $queryParams['q'] = implode(',', $q);
            }
            if ($dateFrom) {
                $queryParams['from-date'] = $dateFrom;
            }
            if ($dateTo) {
                $queryParams['to-date'] = $dateTo;
            }
    
            $queryParams['api-key'] = $this->apiKey;
            $url = 'https://content.guardianapis.com/search';
    
            $response = $this->client->get($url, ['query' => $queryParams]);
            $data = json_decode($response->getBody(), true);
    
            if (isset($data['response']['status']) && $data['response']['status'] !== 'ok') {
                throw new Exception("Error from Guardian API: " . ($data['response']['message'] ?? 'Unknown error'));
            }
    
            return GuardianArticleMapper::mapResponseToSchema($data, $category);
        } catch (Exception $e) {
            Log::error("Error fetching articles from Guardian API: " . $e->getMessage());
            return [];
        }
    }
}
