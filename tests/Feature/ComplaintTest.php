<?php

namespace Tests\Feature;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Events\ComplaintStatusChanged;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    private User $citizen;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->citizen = User::factory()->create(['role' => UserRole::Citizen]);
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    // ── CRUD ──────────────────────────────────────────────

    public function test_citizen_can_file_complaint(): void
    {
        $this->actingAs($this->citizen)
            ->postJson('/api/complaints', [
                'title'       => 'Broken streetlight',
                'description' => 'The light on 5th avenue has been out for a week.',
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.user_id', $this->citizen->id);
    }

    public function test_store_validates_input(): void
    {
        $this->actingAs($this->citizen)
            ->postJson('/api/complaints', ['title' => 'hi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }

    public function test_unauthenticated_cannot_file_complaint(): void
    {
        $this->postJson('/api/complaints', [
            'title'       => 'Test',
            'description' => 'Should fail',
        ])->assertStatus(401);
    }

    // ── Policy / Authorization ────────────────────────────

    public function test_citizen_sees_only_own_complaints(): void
    {
        Complaint::factory()->count(3)->for($this->citizen)->create();
        Complaint::factory()->count(5)->create(); // other citizens

        $response = $this->actingAs($this->citizen)
            ->getJson('/api/complaints');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_sees_all_complaints(): void
    {
        Complaint::factory()->count(3)->for($this->citizen)->create();
        Complaint::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/complaints');

        $response->assertOk();
        $this->assertEquals(8, $response->json('meta.total'));
    }

    public function test_citizen_cannot_view_other_citizens_complaint(): void
    {
        $other = Complaint::factory()->create();

        $this->actingAs($this->citizen)
            ->getJson("/api/complaints/{$other->id}")
            ->assertStatus(403);
    }

    public function test_citizen_cannot_update_status(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->citizen)
            ->patchJson("/api/complaints/{$complaint->id}", ['status' => 'in_progress'])
            ->assertStatus(403);
    }

    public function test_citizen_can_delete_own_open_complaint(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->citizen)
            ->deleteJson("/api/complaints/{$complaint->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('complaints', ['id' => $complaint->id]);
    }

    public function test_citizen_cannot_delete_non_open_complaint(): void
    {
        $complaint = Complaint::factory()->inProgress()->for($this->citizen)->create();

        $this->actingAs($this->citizen)
            ->deleteJson("/api/complaints/{$complaint->id}")
            ->assertStatus(403);
    }

    // ── State machine ─────────────────────────────────────

    public function test_admin_can_transition_open_to_in_progress(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->admin)
            ->patchJson("/api/complaints/{$complaint->id}", ['status' => 'in_progress'])
            ->assertOk()
            ->assertJsonPath('data.status', 'in_progress');
    }

    public function test_illegal_transition_is_rejected(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create([
            'status' => ComplaintStatus::Resolved,
        ]);

        $this->actingAs($this->admin)
            ->patchJson("/api/complaints/{$complaint->id}", ['status' => 'open'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_status_change_dispatches_event(): void
    {
        Event::fake([ComplaintStatusChanged::class]);

        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->admin)
            ->patchJson("/api/complaints/{$complaint->id}", ['status' => 'in_progress']);

        Event::assertDispatched(ComplaintStatusChanged::class, function ($event) use ($complaint) {
            return $event->complaint->id === $complaint->id
                && $event->oldStatus === ComplaintStatus::Open
                && $event->newStatus === ComplaintStatus::InProgress;
        });
    }

    // ── Filtering ─────────────────────────────────────────

    public function test_can_filter_by_status(): void
    {
        Complaint::factory()->count(3)->for($this->citizen)->create();
        Complaint::factory()->inProgress()->count(2)->for($this->citizen)->create();

        $response = $this->actingAs($this->citizen)
            ->getJson('/api/complaints?status=in_progress');

        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_can_search_by_title(): void
    {
        Complaint::factory()->for($this->citizen)->create(['title' => 'Broken water main']);
        Complaint::factory()->for($this->citizen)->create(['title' => 'Noise complaint']);

        $response = $this->actingAs($this->citizen)
            ->getJson('/api/complaints?search=water');

        $this->assertEquals(1, $response->json('meta.total'));
    }

    public function test_complaints_are_paginated(): void
    {
        Complaint::factory()->count(20)->for($this->citizen)->create();

        $response = $this->actingAs($this->citizen)
            ->getJson('/api/complaints');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertCount(15, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    // ── Assign ────────────────────────────────────────────

    public function test_admin_can_assign_complaint_to_self(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->admin)
            ->postJson("/api/complaints/{$complaint->id}/assign")
            ->assertOk()
            ->assertJsonPath('data.assigned_to', $this->admin->id);
    }

    // ── Activity log ──────────────────────────────────────

    public function test_activity_is_logged_on_create(): void
    {
        $this->actingAs($this->citizen)
            ->postJson('/api/complaints', [
                'title'       => 'Audit test',
                'description' => 'Check activity log works.',
            ]);

        $this->assertDatabaseHas('activities', [
            'action'       => 'created',
            'subject_type' => Complaint::class,
        ]);
    }

    public function test_activity_log_records_status_changes(): void
    {
        $complaint = Complaint::factory()->for($this->citizen)->create();

        $this->actingAs($this->admin)
            ->patchJson("/api/complaints/{$complaint->id}", ['status' => 'in_progress']);

        $this->assertDatabaseHas('activities', [
            'action'     => 'updated',
            'subject_id' => $complaint->id,
        ]);
    }
}
