<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    
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

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        
        // Store the ticket information in case the ticket is deleted later
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            return (new MailMessage)
                ->subject('New Support Ticket Created: #' . $this->ticket->id)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('A new support ticket has been created.')
                ->line('Ticket #' . $this->ticket->id . ': ' . $this->ticket->title)
                ->line('Priority: ' . ucfirst($this->ticket->priority))
                ->action('View Ticket', url(route('tickets.show', $this->ticket->id)))
                ->line('Thank you for using our support system!');
        } catch (Exception $e) {
            Log::error('Failed to create TicketCreated email', [
                'error' => $e->getMessage(),
                'ticket_id' => $this->ticket->id ?? 'unknown',
                'notifiable' => $notifiable->id ?? 'unknown'
            ]);
            
            // Return a simple fallback message
            return (new MailMessage)
                ->subject('New Support Ticket Created')
                ->line('A new support ticket has been created in the system.')
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
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
        ];
    }
    
    /**
     * Handle a notification failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('TicketCreated notification failed to send', [
            'error' => $exception->getMessage(),
            'ticket_id' => $this->ticket->id ?? 'unknown',
            'exception' => get_class($exception)
        ]);
    }
} 