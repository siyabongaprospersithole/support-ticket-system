<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['tickets', 'comments', 'activities'])
            ->with(['tickets' => function ($query) {
                $query->latest()->take(5);
            }])
            ->with(['comments' => function ($query) {
                $query->latest()->take(5);
            }])
            ->with(['activities' => function ($query) {
                $query->latest()->take(5);
            }])
            ->paginate(12);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function show(User $user)
    {
        $user->loadCount(['tickets', 'comments', 'activities']);
        $user->load(['tickets' => function ($query) {
            $query->latest()->take(10);
        }]);
        $user->load(['comments' => function ($query) {
            $query->latest()->take(10);
        }]);
        $user->load(['activities' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('Users/Show', [
            'user' => $user
        ]);
    }
} 