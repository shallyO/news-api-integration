<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Integrations\NewsApiIntegration;
use Exception;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    protected $newsApiIntegration;
    protected $articleRepository;

    public function __construct(NewsApiIntegration $newsApiIntegration, ArticleRepository $articleRepository)
    {
        $this->newsApiIntegration = $newsApiIntegration;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Fetch articles based on filters
     *
     * @param array $filters
     * @return array
     */
    public function fetchArticles(array $filters)
    {
        try {
            // Fetch articles from the database using the repository
            $articles = $this->articleRepository->fetchArticles($filters);

            if ($articles->isEmpty()) {
                // Fallback to external API if no data in the database
                $externalArticles = $this->newsApiIntegration->fetchArticles(
                    $filters['category'] ?? null,
                    $filters['query'] ?? null,
                    $filters['date_from'] ?? null,
                    $filters['date_to'] ?? null
                );

                // Save fetched articles to the database using the repository
                $this->articleRepository->saveArticles($externalArticles);

                return $externalArticles;
            }

            return $articles->toArray();
        } catch (Exception $e) {
            // Log the exception
            Log::error("Error in ArticleService: " . $e->getMessage());

            // Re-throw the exception so it can be handled in the controller
            throw new Exception("An error occurred while fetching articles.");
        }
    }
}
