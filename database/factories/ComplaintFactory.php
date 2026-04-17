<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(6),
            'description' => fake()->paragraphs(2, asText: true),
            'status'      => ComplaintStatus::Open,
            'user_id'     => User::factory(),
            'assigned_to' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::InProgress]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::Resolved]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::Rejected]);
    }
}
