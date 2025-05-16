<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Predefined ticket configurations
     */
    private array $ticketConfigs = [
        'critical' => [
            'open' => [
                [
                    'count' => 2,
                    'comments' => 3,
                    'title' => 'Critical: Server Down',
                    'description' => 'The main production server is not responding to requests.',
                ],
            ],
            'closed' => [
                [
                    'count' => 1,
                    'comments' => 4,
                    'title' => 'Critical: Database Connection Issues',
                    'description' => 'Unable to connect to the main database server.',
                ],
            ],
        ],
        'high' => [
            'open' => [
                [
                    'count' => 3,
                    'comments' => 2,
                    'title' => 'High: Payment Processing Error',
                    'description' => 'Customers are unable to complete payments.',
                ],
            ],
            'closed' => [
                [
                    'count' => 2,
                    'comments' => 3,
                    'title' => 'High: Security Vulnerability',
                    'description' => 'Potential security issue identified in login system.',
                ],
            ],
        ],
        'medium' => [
            'open' => [
                [
                    'count' => 4,
                    'comments' => 2,
                    'title' => 'UI Bug in Dashboard',
                    'description' => 'Charts are not displaying correctly in the analytics dashboard.',
                ],
            ],
            'closed' => [
                [
                    'count' => 3,
                    'comments' => 1,
                    'title' => 'Feature Request: Export Data',
                    'description' => 'Add ability to export data in CSV format.',
                ],
            ],
        ],
        'low' => [
            'open' => [
                [
                    'count' => 5,
                    'comments' => 1,
                    'title' => 'Typo in Documentation',
                    'description' => 'Found several typos in the API documentation.',
                ],
            ],
            'closed' => [
                [
                    'count' => 4,
                    'comments' => 2,
                    'title' => 'Update Footer Links',
                    'description' => 'Social media links in the footer need to be updated.',
                ],
            ],
        ],
    ];

    /**
     * Admin ticket configurations
     */
    private array $adminTicketConfigs = [
        [
            'count' => 2,
            'priority' => 'critical',
            'status' => 'open',
            'comments' => 2,
            'title' => 'Admin Critical Issue',
            'description' => 'Critical system maintenance required.',
        ],
        [
            'count' => 1,
            'priority' => 'high',
            'status' => 'open',
            'comments' => 3,
            'title' => 'Admin High Priority Task',
            'description' => 'Security patch deployment needed.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users
        $users = User::factory(5)->create();

        // Create regular user tickets
        foreach ($this->ticketConfigs as $priority => $statusGroups) {
            foreach ($statusGroups as $status => $tickets) {
                foreach ($tickets as $ticket) {
                    $this->createTickets(
                        count: $ticket['count'],
                        priority: $priority,
                        status: $status,
                        commentCount: $ticket['comments'],
                        title: $ticket['title'],
                        description: $ticket['description'],
                        userId: $users->random()->id
                    );
                }
            }
        }

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create admin tickets
        foreach ($this->adminTicketConfigs as $config) {
            $this->createTickets(
                count: $config['count'],
                priority: $config['priority'],
                status: $config['status'],
                commentCount: $config['comments'],
                title: $config['title'],
                description: $config['description'],
                userId: $admin->id
            );
        }
    }

    /**
     * Helper method to create tickets with the given configuration
     */
    private function createTickets(
        int $count,
        string $priority,
        string $status,
        int $commentCount,
        string $title,
        string $description,
        int $userId
    ): void {
        Ticket::factory()
            ->count($count)
            ->state(function () use ($priority, $status, $title, $description) {
                return [
                    'priority' => $priority,
                    'status' => $status,
                    'title' => $title,
                    'description' => $description,
                ];
            })
            ->has(Comment::factory()->count($commentCount))
            ->create([
                'user_id' => $userId,
            ]);
    }
} 