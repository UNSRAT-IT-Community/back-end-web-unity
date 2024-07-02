<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'content',
        'image_url',
        'creator_id'
    ];

    public function users():BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
}
