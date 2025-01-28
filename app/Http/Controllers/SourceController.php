<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    // Get all sources
    public function getSources()
    {
        try {
            $sources = Source::all();

            if ($sources->isEmpty()) {
                return $this->sendError('No sources found', [], 404);
            }

            return $this->sendSuccess('Sources retrieved successfully', $sources);
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving sources: ' . $e->getMessage(), [], 400);
        }
    }
}
