<?php

namespace App\Services;

use App\Helpers\IntegrationProvider;
use App\Integrations\GuardianApiIntegration;
use App\Models\Article;
use App\Models\Preference;
use App\Integrations\NewsApiIntegration;
use App\Integrations\NewYorkTimesApiIntegration;
use Carbon\Carbon;
use Exception;

class ArticleService
{
    protected $newsApiIntegration;
    protected $guidanceApiIntegration;
    protected $newYorkTimesApiIntegration;



    public function __construct(NewsApiIntegration $newsApiIntegration, GuardianApiIntegration $guardianApiIntegration, NewYorkTimesApiIntegration $newYorkTimesApiIntegration)
    {
        $this->newsApiIntegration = $newsApiIntegration;
        $this->guidanceApiIntegration = $guardianApiIntegration;
        $this->newYorkTimesApiIntegration = $newYorkTimesApiIntegration;

    }

    /**
     * Fetch articles based on filters and user preferences
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function fetchArticles(array $filters)
    {
        try {
            // Fetch user preferences, assume user ID is 1 for now
            $preferences = Preference::where('id', 1)->first();
        
            if (!$preferences) {
                throw new Exception('No preferences found. Kindly set preferences to proceed');
            }
        
            // Merge preferences into filters if no specific filter is provided
            $filters['category'] = $filters['category'] ?? $preferences->preferred_category;
            $filters['authors'] = $filters['authors'] ?? $preferences->preferred_author;
        
            // Apply filters to database query
            $articles = Article::query();
        
            // Apply filters
            if (!empty($filters['category'])) {
                $articles->where('category', 'LIKE', '%' . implode('%', (array) $filters['category']) . '%');
            }
        
            if (!empty($filters['query'])) {
                $articles->where('title', 'LIKE', '%' . $filters['query'] . '%');
            }
        
            if (!empty($filters['authors'])) {
                $articles->whereIn('author', (array) $filters['authors']);
            }
        
            if (!empty($filters['date_from'])) {
                $articles->where('published_at', '>=', $filters['date_from']);
            }
        
            if (!empty($filters['date_to'])) {
                $articles->where('published_at', '<=', $filters['date_to']);
            }
        
            // Set default pagination values if not provided
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 10;
        
            // Apply pagination and retrieve articles
            $paginatedArticles = $articles->paginate($perPage, ['*'], 'page', $page);
        
            // If no articles are found, fallback to external API
            if ($paginatedArticles->isEmpty()) {
                // Determine which API to use based on some condition (e.g., 'source' filter, or another parameter)
    
                switch ($preferences->preferred_source) {
                    case IntegrationProvider::NEW_YORK_TIMES :
                        // Fetch articles from the New York Times API
                        $externalArticles = $this->newYorkTimesApiIntegration->fetchArticles(
                            $filters['category'] ?? null,
                            $filters['query'] ?? null,
                            $filters['date_from'] ?? null,
                            $filters['date_to'] ?? null
                        );
                        break;
                    
                    case IntegrationProvider::THE_GUARDIAN :
                        // Fetch articles from The Guardian API (assuming you have a similar integration for The Guardian)
                        $externalArticles = $this->guidanceApiIntegration->fetchArticles(
                            $filters['category'] ?? null,
                            $filters['query'] ?? null,
                            $filters['date_from'] ?? null,
                            $filters['date_to'] ?? null
                        );
                        break;
    
                    case IntegrationProvider::NEWS_API :
                        // Fetch articles from Guidance API
                        $externalArticles = $this->newsApiIntegration->fetchArticles(
                            $filters['category'] ?? null,
                            $filters['query'] ?? null,
                            $filters['date_from'] ?? null,
                            $filters['date_to'] ?? null
                        );
                        break;
    
                    default:
                        throw new Exception('Unsupported API source');
                }
    
                // Save fetched articles to the database
                $this->saveArticles($externalArticles);
        
                // Return the fetched articles
                return $externalArticles;
            }
        
            // Return paginated articles from the database
            return [
                'data' => $paginatedArticles->items(),
                'current_page' => $paginatedArticles->currentPage(),
                'last_page' => $paginatedArticles->lastPage(),
                'per_page' => $paginatedArticles->perPage(),
                'total' => $paginatedArticles->total(),
            ];
        } catch (Exception $e) {
            // Log and throw exception with the message
            throw new Exception('Error fetching articles: ' . $e->getMessage());
        }
    }
    
    

    /**
     * Save articles to the database
     *
     * @param array $articles
     */
    public function saveArticles(array $articles)
    {
        foreach ($articles as $article) {
            // Convert 'published_at' to a valid MySQL datetime format
            $publishedAt = isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : null;

            Article::updateOrCreate(
                ['url' => $article['url']], // Ensure no duplicate articles by URL
                [
                    'source_name' => $article['source']['name'] ?? null,
                    'author' => $article['author'] ?? null,
                    'title' => $article['title'],
                    'description' => $article['description'] ?? null,
                    'url' => $article['url'],
                    'url_to_image' => $article['urlToImage'] ?? null,
                    'published_at' => $publishedAt,
                    'content' => $article['content'] ?? null,
                    'category' => $article['category'] ?? null,
                ]
            );
        }
    }
}
