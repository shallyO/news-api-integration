<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    
    // The attributes that are mass assignable
    protected $fillable = [
        'preferred_sources',
        'preferred_categories',
        'preferred_authors',
    ];

    // Cast to array or JSON as needed (optional)
    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

}
