<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_comment_on_complaint(): void
    {
        $citizen = User::factory()->create(['role' => UserRole::Citizen]);
        $complaint = Complaint::factory()->for($citizen)->create();

        $this->actingAs($citizen)
            ->postJson("/api/complaints/{$complaint->id}/comments", [
                'body' => 'Any updates on this?',
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.body', 'Any updates on this?');
    }

    public function test_admin_can_comment_on_any_complaint(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $complaint = Complaint::factory()->create();

        $this->actingAs($admin)
            ->postJson("/api/complaints/{$complaint->id}/comments", [
                'body' => 'We are looking into it.',
            ])
            ->assertStatus(201);
    }

    public function test_other_citizen_cannot_comment(): void
    {
        $other = User::factory()->create(['role' => UserRole::Citizen]);
        $complaint = Complaint::factory()->create();

        $this->actingAs($other)
            ->postJson("/api/complaints/{$complaint->id}/comments", [
                'body' => 'Should be denied.',
            ])
            ->assertStatus(403);
    }

    public function test_comments_are_listed_oldest_first(): void
    {
        $citizen = User::factory()->create(['role' => UserRole::Citizen]);
        $complaint = Complaint::factory()->for($citizen)->create();

        $this->actingAs($citizen)
            ->postJson("/api/complaints/{$complaint->id}/comments", ['body' => 'First']);
        $this->actingAs($citizen)
            ->postJson("/api/complaints/{$complaint->id}/comments", ['body' => 'Second']);

        $response = $this->actingAs($citizen)
            ->getJson("/api/complaints/{$complaint->id}/comments");

        $response->assertOk();
        $this->assertEquals('First', $response->json('data.0.body'));
        $this->assertEquals('Second', $response->json('data.1.body'));
    }
}
