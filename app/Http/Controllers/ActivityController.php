<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with(['subject', 'causer'])
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->through(function ($activity) {
                $ticket = $activity->ticket;
                
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'properties' => $activity->properties,
                    'details' => $activity->getFormattedDetails(),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'subject_type' => class_basename($activity->subject_type),
                    'subject_id' => $activity->subject_id,
                    'ticket' => $ticket ? [
                        'id' => $ticket->id,
                        'title' => $ticket->title,
                        'number' => $ticket->ticket_number,
                        'url' => route('tickets.show', $ticket->id)
                    ] : null,
                    'causer' => $activity->causer ? [
                        'name' => $activity->causer->name,
                        'email' => $activity->causer->email,
                    ] : null,
                    'icon' => $activity->icon,
                    'color' => $activity->color,
                ];
            });

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type']),
        ]);
    }

}
