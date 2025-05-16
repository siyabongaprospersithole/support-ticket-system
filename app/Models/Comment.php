<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\LogsActivity;

class Comment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'content',
        'ticket_id',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            $comment->ticket->recordActivity(
                'commented',
                "New comment added by {$comment->user->name}",
                ['comment_id' => $comment->id]
            );
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 