<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewTicketComment;

class CommentManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that a user can add a comment to a ticket.
     */
    public function test_user_can_add_comment_to_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $commentData = [
            'content' => 'This is a test comment on the ticket.'
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), $commentData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comment added successfully.');

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment on the ticket.',
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test that comment validation works.
     */
    public function test_comment_requires_content(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), [
                'content' => ''
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'There was a problem adding your comment. Please try again.');
        
        $this->assertDatabaseMissing('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => '',
        ]);
    }

    /**
     * Test that guests cannot add comments.
     */
    public function test_guest_cannot_add_comment(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), [
                'content' => 'This comment should not be allowed.'
            ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'content' => 'This comment should not be allowed.',
            'ticket_id' => $ticket->id,
        ]);
    }

    /**
     * Test notification is sent when a comment is added by someone other than the ticket creator.
     */
    public function test_notification_sent_when_comment_added(): void
    {
        Notification::fake();

        $ticketCreator = User::factory()->create();
        $commenter = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $ticketCreator->id]);

        $this->actingAs($commenter)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), [
                'content' => 'This comment should trigger a notification.'
            ]);

        Notification::assertSentTo([$ticketCreator], NewTicketComment::class);
    }

    /**
     * Test notification is not sent to the ticket creator when they comment on their own ticket.
     */
    public function test_no_notification_when_creator_comments_on_own_ticket(): void
    {
        Notification::fake();

        $ticketCreator = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $ticketCreator->id]);

        $this->actingAs($ticketCreator)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), [
                'content' => 'Commenting on my own ticket.'
            ]);

        Notification::assertNotSentTo([$ticketCreator], NewTicketComment::class);
    }

    /**
     * Test that other commenters receive notifications when a new comment is added.
     */
    public function test_other_commenters_get_notifications(): void
    {
        Notification::fake();

        $ticketCreator = User::factory()->create();
        $firstCommenter = User::factory()->create();
        $secondCommenter = User::factory()->create();
        
        $ticket = Ticket::factory()->create(['user_id' => $ticketCreator->id]);
        
        // Add first comment
        Comment::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $firstCommenter->id,
            'content' => 'First comment'
        ]);
        
        // Add second comment and check notifications
        $this->actingAs($secondCommenter)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.comments.store', $ticket->id), [
                'content' => 'Second comment that should notify the first commenter and creator.'
            ]);
        
        Notification::assertSentTo([$ticketCreator, $firstCommenter], NewTicketComment::class);
    }
} 