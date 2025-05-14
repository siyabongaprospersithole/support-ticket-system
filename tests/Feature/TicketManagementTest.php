<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user can view ticket listing page.
     */
    public function test_user_can_view_ticket_listing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('tickets.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tickets/Index'));
    }

    /**
     * Test user can view the create ticket form.
     */
    public function test_user_can_view_create_ticket_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('tickets.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tickets/Create'));
    }

    /**
     * Test user can create a new ticket.
     */
    public function test_user_can_create_a_ticket(): void
    {
        $user = User::factory()->create();

        $ticketData = [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket description',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.store'), $ticketData);

        $response->assertRedirect(route('tickets.index'));
        $response->assertSessionHas('success', 'Ticket created successfully.');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket description',
            'priority' => 'medium',
            'status' => 'open',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test validation when creating a ticket.
     */
    public function test_ticket_creation_validates_input(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.store'), [
                'title' => '',
                'description' => '',
                'priority' => 'invalid-priority',
            ]);

        $response->assertSessionHasErrors(['title', 'description', 'priority']);
    }

    /**
     * Test user can view a ticket.
     */
    public function test_user_can_view_a_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('tickets.show', $ticket->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Tickets/Show')
            ->has('ticket')
            ->has('comments')
            ->has('statuses')
        );
    }

    /**
     * Test user can update ticket status.
     */
    public function test_user_can_update_ticket_status(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->patch(route('tickets.update', $ticket->id), [
                'status' => 'closed'
            ]);

        $response->assertRedirect(route('tickets.show', $ticket->id));
        $response->assertSessionHas('success', 'Ticket status updated successfully.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed'
        ]);
    }

    /**
     * Test guest cannot access ticket pages.
     */
    public function test_guest_cannot_access_ticket_pages(): void
    {
        $ticket = Ticket::factory()->create();

        $this->get(route('tickets.index'))->assertRedirect(route('login'));
        $this->get(route('tickets.create'))->assertRedirect(route('login'));
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('tickets.store'))->assertRedirect(route('login'));
        $this->get(route('tickets.show', $ticket->id))->assertRedirect(route('login'));
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->patch(route('tickets.update', $ticket->id))->assertRedirect(route('login'));
    }

    /**
     * Test ticket filtering functionality.
     */
    public function test_tickets_can_be_filtered(): void
    {
        $user = User::factory()->create();
        
        // Create tickets with different statuses and priorities
        $openTicket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
            'priority' => 'high',
            'title' => 'Open High Priority Ticket'
        ]);
        
        $closedTicket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'closed',
            'priority' => 'low',
            'title' => 'Closed Low Priority Ticket'
        ]);

        // Test status filter
        $response = $this->actingAs($user)
            ->get(route('tickets.index', ['status' => 'open']));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Tickets/Index')
            ->has('tickets', 1)
            ->has('filters')
        );

        // Test priority filter
        $response = $this->actingAs($user)
            ->get(route('tickets.index', ['priority' => 'low']));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Tickets/Index')
            ->has('tickets', 1)
            ->has('filters')
        );

        // Test search filter
        $response = $this->actingAs($user)
            ->get(route('tickets.index', ['search' => 'High Priority']));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Tickets/Index')
            ->has('tickets', 1)
            ->has('filters')
        );
    }
} 