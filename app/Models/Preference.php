<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    // The attributes that are mass assignable
    protected $fillable = [
        'preferred_source',
        'preferred_category',
        'preferred_author',
    ];

    /**
     * Define a one-to-one relationship with the Source model.
     */
    public function source()
    {
        return $this->hasOne(Source::class, 'id', 'preferred_source');
    }
}
