<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'photo_url',
        'caption',
        'creator_id'
    ];

    public function users():BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
}
