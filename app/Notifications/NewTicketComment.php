<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class NewTicketComment extends Notification implements ShouldQueue
{
    use Queueable;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    public function __construct(
        public Comment $comment
    ) {
        // Store the comment information in case the comment is deleted later
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        try {
            $ticket = $this->comment->ticket;
            
            return (new MailMessage)
                ->subject("New comment on Ticket #{$ticket->id}")
                ->greeting("Hello " . $notifiable->name . "!")
                ->line("A new comment has been added to ticket: {$ticket->title}")
                ->line("Comment by: {$this->comment->user->name}")
                ->line($this->comment->content)
                ->action('View Ticket', route('tickets.show', $ticket->id))
                ->line('Thank you for using our support system!');
        } catch (Exception $e) {
            Log::error('Failed to create NewTicketComment email', [
                'error' => $e->getMessage(),
                'comment_id' => $this->comment->id ?? 'unknown',
                'ticket_id' => $this->comment->ticket->id ?? 'unknown',
                'notifiable' => $notifiable->id ?? 'unknown'
            ]);
            
            // Return a simple fallback message
            return (new MailMessage)
                ->subject('New Comment Added')
                ->line('A new comment has been added to your ticket.')
                ->line('Please log in to view the details.');
        }
    }
    
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'ticket_id' => $this->comment->ticket->id,
            'user_id' => $this->comment->user->id,
            'content' => substr($this->comment->content, 0, 100),
        ];
    }
    
    /**
     * Handle a notification failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('NewTicketComment notification failed to send', [
            'error' => $exception->getMessage(),
            'comment_id' => $this->comment->id ?? 'unknown',
            'ticket_id' => $this->comment->ticket->id ?? 'unknown',
            'exception' => get_class($exception)
        ]);
    }
} 