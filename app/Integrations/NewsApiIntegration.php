<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Log;
use JCobhams\NewsApi\NewsApi;
use Carbon\Carbon;

class NewsApiIntegration
{
    protected $newsApi;

    public function __construct()
    {
        $this->newsApi = new NewsApi(env('NEWSAPI_KEY'));
    }

    /**
     * Fetch articles from NewsAPI
     *
     * @param string|null $searchQuery
     * @param string|null $category
     * @param string|null $country
     * @param string|null $date (Format: 'YYYY-MM-DD')
     * @param int|null $pageSize
     * @param int|null $page
     * @return array
     */
    public function fetchArticles($searchQuery = null, $category = null, $country = null, $date = null, $pageSize = 20, $page = 1)
    {
        try {
            // Set up parameters for the API call
            $params = [
                'q' => $searchQuery,
                'category' => $category,
                'country' => $country,
                'pageSize' => $pageSize,
                'page' => $page,
            ];

            // If a date is provided, calculate the previous date
            if ($date) {
                $fromDate = Carbon::parse($date)->startOfDay()->toDateString();  // Set 'from' as the start of the day
                $toDate = Carbon::parse($date)->subDay()->endOfDay()->toDateString();  // Set 'to' as the previous day

                // Add the date range to the parameters
                $params['from'] = $fromDate;
                $params['to'] = $toDate;
            }

            // Make the API call with the specified parameters
            $articles = $this->newsApi->getEverything(
                $params['q'],
                null, // Sources - optional
                null, // Domains - optional
                null, // Exclude domains - optional
                $params['from'] ?? null, // From date - optional
                $params['to'] ?? null, // To date - optional
                null, // Language - optional
                'publishedAt', // Sort by published date
                $params['pageSize'],
                $params['page']
            );

            return $articles->articles ?? [];
        } catch (\Exception $e) {
            Log::error("Error fetching articles from NewsAPI: " . $e->getMessage());
            return [];
        }
    }
}
