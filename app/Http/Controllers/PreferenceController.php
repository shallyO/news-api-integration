<?php

namespace App\Http\Controllers;

use App\Services\PreferenceService;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    protected $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    // Get preferences
    public function getPreferences()
    {
        try {
            $preference = $this->preferenceService->getPreferences();
            return $this->sendSuccess('Preferences retrieved successfully', $preference);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 400);
        }
    }

    // Update preferences
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'preferred_source' => 'required|exists:sources,id',
            'preferred_category' => 'nullable|string',
            'preferred_author' => 'nullable|string',
        ]);

        $data = $request->only(['preferred_source', 'preferred_category', 'preferred_author']);

        try {
            $preference = $this->preferenceService->updatePreferences($data);
            return $this->sendSuccess('Preferences updated successfully', $preference);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 400);
        }
    }
}
