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

class TicketStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;
    
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
    public function __construct(Ticket $ticket, string $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        
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
            $statusText = ucfirst($this->ticket->status);
            $oldStatusText = ucfirst($this->oldStatus);
            
            return (new MailMessage)
                ->subject("Ticket #" . $this->ticket->id . " Status Changed")
                ->greeting("Hello " . $notifiable->name . "!")
                ->line("The status of your ticket has been updated.")
                ->line("Ticket #" . $this->ticket->id . ": " . $this->ticket->title)
                ->line("Status changed from **{$oldStatusText}** to **{$statusText}**")
                ->action('View Ticket', url(route('tickets.show', $this->ticket->id)))
                ->line('Thank you for using our support system!');
        } catch (Exception $e) {
            Log::error('Failed to create TicketStatusChanged email', [
                'error' => $e->getMessage(),
                'ticket_id' => $this->ticket->id ?? 'unknown',
                'old_status' => $this->oldStatus ?? 'unknown',
                'new_status' => $this->ticket->status ?? 'unknown',
                'notifiable' => $notifiable->id ?? 'unknown'
            ]);
            
            // Return a simple fallback message
            return (new MailMessage)
                ->subject('Ticket Status Update')
                ->line('A ticket status has been updated in the system.')
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
            'status' => $this->ticket->status,
            'old_status' => $this->oldStatus,
        ];
    }
    
    /**
     * Handle a notification failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('TicketStatusChanged notification failed to send', [
            'error' => $exception->getMessage(),
            'ticket_id' => $this->ticket->id ?? 'unknown',
            'old_status' => $this->oldStatus ?? 'unknown',
            'new_status' => $this->ticket->status ?? 'unknown',
            'exception' => get_class($exception)
        ]);
    }
} 