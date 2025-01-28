<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ArticleService; // Assuming your fetchArticles method is in a service
use Illuminate\Support\Facades\Log;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches articles based on user preferences and stores them.';

    /**
     * The article service instance.
     *
     * @var \App\Services\ArticleService
     */
    protected $articleService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\ArticleService $articleService
     */
    public function __construct(ArticleService $articleService)
    {
        parent::__construct();

        $this->articleService = $articleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Log that the command has started
        Log::info('FetchArticles command started at ' . now());
    
        // Define the filters (you can customize as needed)
        $filters = [
            'category' => 'yoruba', 
            'authors' => null, 
            'page' => 1,
            'per_page' => 50,
        ];
    
        try {
            // Call the fetchArticles method from the ArticleService
            $articles = $this->articleService->fetchArticles($filters);
    
            // Dump the response
            dd($articles); // This will dump the fetched articles and stop the script
    
            // Log the number of articles fetched
            Log::info('Fetched ' . count($articles) . ' articles successfully.');
    
            // Output the number of articles fetched (or other useful information)
            $this->info('Fetched ' . count($articles) . ' articles successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching articles: ' . $e->getMessage());
    
            // Handle the error (log it or display an error message)
            $this->error('Error fetching articles: ' . $e->getMessage());
        }
    
        // Log that the command has finished
        Log::info('FetchArticles command finished at ' . now());
    }
    
}

