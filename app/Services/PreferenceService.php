<?php

namespace App\Services;

use App\Models\Preference;
use App\Models\Source;
use Exception;

class PreferenceService
{
    public function getPreferences()
    {
        try {
            // Retrieve the preference with the related source using eager loading
            $preference = Preference::with('source:id,name')  // Eager load the source relationship and select only 'id' and 'name' columns
                ->first();

            if ($preference) {
                // Add the source name to the preference object
                $preference->source_name = $preference->source ? $preference->source->name : null;
                return $preference;
            }
            throw new Exception('Preferences not found');
        } catch (Exception $e) {
            throw new Exception('Error retrieving preferences: ' . $e->getMessage());
        }
    }

    public function updatePreferences($data)
    {
        try {
            // Validate the source
            $source = Source::find($data['preferred_source']);
            if (!$source) {
                throw new Exception('Invalid source ID');
            }

            // Use updateOrCreate to update or create preference with id = 1
            $preference = Preference::updateOrCreate(
                ['id' => 1], // Ensure we're working with preference with id = 1
                [
                    'preferred_source' => $data['preferred_source'],
                    'preferred_category' => $data['preferred_category'] ?? null,
                    'preferred_author' => $data['preferred_author'] ?? null,
                ]
            );

            return $preference;
        } catch (Exception $e) {
            throw new Exception('Error updating preferences: ' . $e->getMessage());
        }
    }

}
