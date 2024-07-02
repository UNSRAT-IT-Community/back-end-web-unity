<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpcomingEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'content',
        'start_time',
        'end_time',
        'image_url',
        'creator_id'
    ];

    public function users():BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
}

