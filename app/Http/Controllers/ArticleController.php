<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Fetch articles based on the provided filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchArticles(Request $request)
    {
        try {
            $filters = $request->only(['query', 'category', 'authors', 'date_from', 'date_to']);

            // Fetch articles using the ArticleService
            $articles = $this->articleService->fetchArticles($filters);

            // Return success response
            return $this->sendSuccess($articles, 'Articles fetched successfully');
        } catch (\Exception $e) {
            // Log the exception

            // Return error response
            return $this->sendError('An error occurred while fetching articles', []);
        }
    }
}
