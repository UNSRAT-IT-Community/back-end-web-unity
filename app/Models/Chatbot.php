<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chatbot extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'knowledge',
        'user_id'
    ];

    public function users():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
