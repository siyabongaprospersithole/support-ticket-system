<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Comment;
use App\Notifications\NewTicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string'
            ]);

            DB::beginTransaction();

            $comment = new Comment([
                'content' => $validated['content'],
                'user_id' => Auth::id(),
            ]);

            $ticket->comments()->save($comment);

            // Notify the ticket creator if it's not their own comment
            if ($ticket->user_id !== Auth::id()) {
                $ticket->user->notify(new NewTicketComment($comment));
            }

            // Notify other commenters (except the ticket creator and current commenter)
            $notifiedUsers = collect([$ticket->user_id, Auth::id()]);
            
            $ticket->comments()
                ->with('user')
                ->get()
                ->pluck('user')
                ->unique('id')
                ->whereNotIn('id', $notifiedUsers)
                ->each(function ($user) use ($comment) {
                    $user->notify(new NewTicketComment($comment));
                });

            DB::commit();
            
            return back()->with('success', 'Comment added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add comment to ticket #' . $ticket->id, [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'content' => $request->content ?? 'No content',
            ]);
            
            return back()->with('error', 'There was a problem adding your comment. Please try again.');
        }
    }
} 