<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'properties',
        'causer_id',
        'causer_type',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who caused the activity.
     */
    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Get the comment associated with this activity.
     */
    public function comment(): ?BelongsTo
    {
        if ($this->subject_type === Comment::class) {
            return $this->belongsTo(Comment::class, 'subject_id');
        }
        
        if ($this->type === 'commented' && isset($this->properties['comment_id'])) {
            return $this->belongsTo(Comment::class, 'properties->comment_id');
        }

        return null;
    }

    /**
     * Get the ticket associated with this activity.
     */
    public function ticket(): ?BelongsTo
    {
        if ($this->subject_type === Ticket::class) {
            return $this->belongsTo(Ticket::class, 'subject_id');
        }

        return null;
    }

    /**
     * Get the associated ticket, either directly or through a comment.
     */
    public function getTicketAttribute()
    {
        if ($this->subject_type === Ticket::class) {
            return $this->subject;
        }
        
        if ($this->subject_type === Comment::class) {
            return $this->subject?->ticket;
        }

        if ($this->type === 'commented' && isset($this->properties['comment_id'])) {
            $comment = Comment::find($this->properties['comment_id']);
            return $comment?->ticket;
        }

        return null;
    }

    /**
     * Get formatted details for the activity.
     */
    public function getFormattedDetails(): ?array
    {
        if ($this->type === 'commented') {
            if ($this->subject_type === Comment::class) {
                return ['content' => $this->subject?->content];
            }
            
            if (isset($this->properties['comment_id'])) {
                $comment = Comment::find($this->properties['comment_id']);
                return $comment ? ['content' => $comment->content] : null;
            }
        }

        if ($this->type === 'status_changed') {
            return [
                'old_status' => ucfirst($this->properties['old'] ?? ''),
                'new_status' => ucfirst($this->properties['new'] ?? '')
            ];
        }

        if ($this->type === 'priority_changed') {
            return [
                'old_priority' => ucfirst($this->properties['old'] ?? ''),
                'new_priority' => ucfirst($this->properties['new'] ?? '')
            ];
        }

        if ($this->type === 'updated') {
            $changes = [];
            foreach ($this->properties as $field => $change) {
                if (is_array($change) && isset($change['old'], $change['new'])) {
                    $changes[$field] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => $change['old'],
                        'new' => $change['new']
                    ];
                }
            }
            return ['changes' => $changes];
        }

        return null;
    }

    /**
     * Get the icon for the activity type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'created' => 'plus-circle',
            'updated' => 'pencil',
            'deleted' => 'trash',
            'commented' => 'comment',
            'status_changed' => 'refresh',
            'priority_changed' => 'flag',
            'assigned' => 'user-plus',
            default => 'circle',
        };
    }

    /**
     * Get the color for the activity type.
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'commented' => 'purple',
            'status_changed' => 'orange',
            'priority_changed' => 'yellow',
            'assigned' => 'indigo',
            default => 'gray',
        };
    }
}
