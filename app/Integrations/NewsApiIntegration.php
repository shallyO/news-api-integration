<?php

namespace App\Integrations;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class NewsApiIntegration
{
    protected $client;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = env('NEWSAPI_KEY');  // Assuming you have an environment variable for the API key
    }

    /**
     * Fetch articles from NewsAPI.
     *
     * @param string|null $category
     * @param string|null $query
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function fetchArticles($category = null, $query = null, $dateFrom = null, $dateTo = null)
    {
        try {
            // Build the query string based on the provided filters
            $queryParams = [];

            // If category or query is provided, build the `q` parameter
            $q = [];

            // Ensure category is a string or an array of strings
            if ($category) {
                $q[] = is_array($category) ? implode(',', $category) : (string)$category;
            }

            // Ensure query is a string or an array of strings
            if ($query) {
                $q[] = is_array($query) ? implode(',', $query) : (string)$query;
            }

            // Ensure `q` is a string (comma-separated)
            if (!empty($q)) {
                $queryParams['q'] = implode(',', $q);
            }

            if ($dateFrom) {
                $queryParams['from'] = $dateFrom;
            }
            if ($dateTo) {
                $queryParams['to'] = $dateTo;
            }

            // Always include the API key in the request
            $queryParams['apiKey'] = $this->apiKey;

            // Log the generated query parameters for debugging
            Log::info('Sending request to NewsAPI with parameters:', $queryParams);

            // Build the URL
            $url = 'https://newsapi.org/v2/everything';

            // Send the GET request
            $response = $this->client->get($url, [
                'query' => $queryParams
            ]);

            // Decode the response
            $data = json_decode($response->getBody(), true);

            // Log the response for debugging
            Log::info('Response from NewsAPI:', $data);

            // Check if the response status is an error
            if ($data['status'] == 'error') {
                throw new Exception("Error from NewsAPI: " . $data['message']);
            }

            return $data['articles'] ?? [];

        } catch (Exception $e) {
            // Log the exception message
            Log::error("Error fetching articles from NewsAPI: " . $e->getMessage());

            // Rethrow the exception with a new message
            throw new Exception("Error fetching articles from NewsAPI: " . $e->getMessage());
        }
    }
}
