<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the tickets.
     */
    public function index(Request $request)
    {
        try {
            $query = Ticket::with('user')
                         ->withCount('comments')
                         ->when($request->sortBy, function($query, $sortBy) use ($request) {
                             $direction = $request->sortDirection === 'desc' ? 'desc' : 'asc';
                             return $query->orderBy($sortBy, $direction);
                         }, function($query) {
                             return $query->latest();
                         });

            // Search filter
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            // User filter
            if ($request->user_filter) {
                $query->where('user_id', $request->user_filter);
            }

            // Status filter
            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Priority filter
            if ($request->priority) {
                $query->where('priority', $request->priority);
            }

            // Date range filter
            if ($request->dateFrom) {
                $query->whereDate('created_at', '>=', $request->dateFrom);
            }
            if ($request->dateTo) {
                $query->whereDate('created_at', '<=', $request->dateTo);
            }

            $tickets = $query->paginate(10)->through(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at,
                    'comments_count' => $ticket->comments_count,
                    'user' => $ticket->user ? [
                        'id' => $ticket->user->id,
                        'name' => $ticket->user->name,
                        'email' => $ticket->user->email,
                    ] : null,
                ];
            });

            // Get users for the filter dropdown
            $users = User::select('id', 'name', 'email')
                        ->orderBy('name')
                        ->get();


            return Inertia::render('Tickets/Index', [
                'tickets' => $tickets,
                'filters' => $request->only(['search', 'status', 'priority', 'dateFrom', 'dateTo', 'sortBy', 'sortDirection', 'user_filter']),
                'users' => $users,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching tickets: ' . $e->getMessage(), [
                'exception' => $e,
                'filters' => $request->all()
            ]);
            
            return Inertia::render('Tickets/Index', [
                'tickets' => [],
                'filters' => $request->only(['search', 'status', 'priority', 'dateFrom', 'dateTo']),
                'error' => 'An error occurred while fetching tickets. Please try again.'
            ]);
        }
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'priorities' => Ticket::getPriorities()
        ]);
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(TicketStoreRequest $request)
    {
        try {
            // Start database transaction
            DB::beginTransaction();
            
            $ticket = new Ticket([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => Ticket::STATUS_OPEN,
            ]);

            Auth::user()->tickets()->save($ticket);
            
            // Commit the transaction
            DB::commit();
            
            // Send notifications after successful DB transaction
            try {
                // Send notification to admin users
                $admins = User::where('email', 'like', '%@admin%')->get();
                if ($admins->count() > 0) {
                    Notification::send($admins, new TicketCreated($ticket));
                }
                
                // Notify the ticket creator
                Auth::user()->notify(new TicketCreated($ticket));
            } catch (Exception $e) {
                // Log notification errors but don't fail the request
                Log::error('Error sending ticket creation notifications: ' . $e->getMessage(), [
                    'exception' => $e,
                    'ticket_id' => $ticket->id
                ]);
            }

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket created successfully.');
                
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            DB::rollBack();
            
            Log::error('Error creating ticket: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create ticket. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        try {
            return Inertia::render('Tickets/Show', [
                'ticket' => $ticket->load('user'),
                'comments' => $ticket->comments()->with('user')->get(),
                'statuses' => Ticket::getStatuses()
            ]);
        } catch (Exception $e) {
            Log::error('Error showing ticket: ' . $e->getMessage(), [
                'exception' => $e,
                'ticket_id' => $ticket->id
            ]);
            
            return redirect()->route('tickets.index')
                ->with('error', 'Failed to load ticket details. Please try again.');
        }
    }

    /**
     * Update the specified ticket status in storage.
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        try {
            // Start database transaction
            DB::beginTransaction();
            
            // Store old status for notification
            $oldStatus = $ticket->status;
            
            // Update status
            $ticket->status = $request->status;
            $ticket->save();
            
            // Commit the transaction
            DB::commit();
            
            // Only send notification if status actually changed
            if ($oldStatus !== $ticket->status) {
                try {
                    // Notify the ticket owner
                    $ticket->user->notify(new TicketStatusChanged($ticket, $oldStatus));
                    
                    // Notify users who have commented on this ticket (except the ticket owner)
                    $commenters = $ticket->comments()
                        ->with('user')
                        ->get()
                        ->pluck('user')
                        ->unique('id')
                        ->reject(function ($user) use ($ticket) {
                            return $user->id === $ticket->user_id;
                        });
                        
                    Notification::send($commenters, new TicketStatusChanged($ticket, $oldStatus));
                } catch (Exception $e) {
                    // Log notification errors but don't fail the request
                    Log::error('Error sending ticket status change notifications: ' . $e->getMessage(), [
                        'exception' => $e,
                        'ticket_id' => $ticket->id
                    ]);
                }
            }

            return redirect()->route('tickets.show', $ticket->id)
                ->with('success', 'Ticket status updated successfully.');
                
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            DB::rollBack();
            
            Log::error('Error updating ticket status: ' . $e->getMessage(), [
                'exception' => $e,
                'ticket_id' => $ticket->id,
                'requested_status' => $request->status
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update ticket status. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Bulk update tickets.
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tickets,id',
                'status' => 'sometimes|in:open,closed',
                'priority' => 'sometimes|in:critical,high,medium,low'
            ]);

            DB::beginTransaction();

            $tickets = Ticket::whereIn('id', $request->ids)->get();
            
            foreach ($tickets as $ticket) {
                $oldStatus = $ticket->status;
                
                if ($request->has('status')) {
                    $ticket->status = $request->status;
                }
                
                if ($request->has('priority')) {
                    $ticket->priority = $request->priority;
                }
                
                $ticket->save();

                // Notify users if status changed
                if ($request->has('status') && $oldStatus !== $ticket->status) {
                    try {
                        // Notify the ticket owner
                        $ticket->user->notify(new TicketStatusChanged($ticket, $oldStatus));
                        
                        // Notify users who have commented on this ticket (except the ticket owner)
                        $commenters = $ticket->comments()
                            ->with('user')
                            ->get()
                            ->pluck('user')
                            ->unique('id')
                            ->reject(function ($user) use ($ticket) {
                                return $user->id === $ticket->user_id;
                            });
                            
                        Notification::send($commenters, new TicketStatusChanged($ticket, $oldStatus));
                    } catch (Exception $e) {
                        // Log notification errors but don't fail the request
                        Log::error('Error sending ticket status change notifications: ' . $e->getMessage(), [
                            'exception' => $e,
                            'ticket_id' => $ticket->id
                        ]);
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Selected tickets have been updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating tickets in bulk: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return back()->with('error', 'An error occurred while updating the tickets. Please try again.');
        }
    }

    /**
     * Bulk delete tickets.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tickets,id'
            ]);

            Ticket::whereIn('id', $request->ids)->delete();

            return back()->with('success', 'Selected tickets have been deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting tickets in bulk: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return back()->with('error', 'An error occurred while deleting the tickets. Please try again.');
        }
    }
}
