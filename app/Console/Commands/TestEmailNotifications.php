<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email-notifications {email : The email address to send test notifications to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the email notification system by sending test emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Testing email notifications. Sending to: {$email}");
        
        // Create or find a test user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
            ]
        );
        
        // Create a sample ticket
        $ticket = Ticket::create([
            'title' => 'Test Ticket for Email Notification',
            'description' => 'This is a test ticket to verify that email notifications are working correctly.',
            'status' => 'open',
            'priority' => 'medium',
            'user_id' => $user->id,
        ]);
        
        // Send ticket created notification
        $this->info('Sending ticket created notification...');
        $user->notify(new TicketCreated($ticket));
        $this->info('✓ Ticket created notification sent!');
        
        // Send ticket status changed notification
        $this->info('Sending ticket status changed notification...');
        $oldStatus = $ticket->status;
        $ticket->status = 'closed';
        $ticket->save();
        
        $user->notify(new TicketStatusChanged($ticket, $oldStatus));
        $this->info('✓ Ticket status changed notification sent!');
        
        $this->info('All test notifications have been sent. Please check the specified email inbox.');
        
        return Command::SUCCESS;
    }
} 