<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(3),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['open', 'closed']),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the ticket is open.
     */
    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open',
            ];
        });
    }

    /**
     * Indicate that the ticket is closed.
     */
    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'closed',
            ];
        });
    }

    /**
     * Indicate that the ticket is critical priority.
     */
    public function critical(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'critical',
            ];
        });
    }

    /**
     * Indicate that the ticket is high priority.
     */
    public function high(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }

    /**
     * Indicate that the ticket is medium priority.
     */
    public function medium(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'medium',
            ];
        });
    }

    /**
     * Indicate that the ticket is low priority.
     */
    public function low(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'low',
            ];
        });
    }
} 