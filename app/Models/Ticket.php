<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\LogsActivity;

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'user_id',
    ];

    // Priority constants
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    // Get available priorities
    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    // Get available statuses
    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    // Relationship to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($ticket) {
            if ($ticket->isDirty('status')) {
                $ticket->recordActivity(
                    'status_changed',
                    "Status changed from {$ticket->getOriginal('status')} to {$ticket->status}",
                    [
                        'old' => $ticket->getOriginal('status'),
                        'new' => $ticket->status
                    ]
                );
            }

            if ($ticket->isDirty('priority')) {
                $ticket->recordActivity(
                    'priority_changed',
                    "Priority changed from {$ticket->getOriginal('priority')} to {$ticket->priority}",
                    [
                        'old' => $ticket->getOriginal('priority'),
                        'new' => $ticket->priority
                    ]
                );
            }
        });
    }
}
