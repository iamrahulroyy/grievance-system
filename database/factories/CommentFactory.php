<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body'         => fake()->paragraph(),
            'complaint_id' => Complaint::factory(),
            'user_id'      => User::factory(),
        ];
    }
}
