<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ComplaintController extends Controller
{
    public function __construct(private readonly ComplaintService $complaints)
    {
    }

    /**
     * List complaints.
     *
     * Citizens see only their own complaints. Admins see everything.
     * Supports filtering via query params: `status`, `search`, `sort`.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Complaint::class);

        $query = Complaint::query()->with(['user', 'assignee']);

        // Citizens only see their own complaints.
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        // ?status=open
        $query->when($request->query('status'), function ($q, $status) {
            $enum = ComplaintStatus::tryFrom($status);
            if ($enum) {
                $q->where('status', $enum);
            }
        });

        // ?search=broken streetlight
        $query->when($request->query('search'), function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        });

        // ?assigned_to=me
        $query->when($request->query('assigned_to') === 'me', function ($q) use ($request) {
            $q->where('assigned_to', $request->user()->id);
        });

        // ?sort=-created_at (prefix with - for descending, default: newest first)
        $sortField = $request->query('sort', '-created_at');
        $direction = str_starts_with($sortField, '-') ? 'desc' : 'asc';
        $column = ltrim($sortField, '-');

        $allowed = ['created_at', 'updated_at', 'status', 'title'];
        if (in_array($column, $allowed, true)) {
            $query->orderBy($column, $direction);
        } else {
            $query->latest();
        }

        return ComplaintResource::collection($query->paginate(perPage: 15));
    }

    /**
     * View a single complaint.
     */
    public function show(Complaint $complaint): ComplaintResource
    {
        Gate::authorize('view', $complaint);

        return new ComplaintResource($complaint->load(['user', 'assignee']));
    }

    /**
     * File a new complaint.
     */
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        Gate::authorize('create', Complaint::class);

        $complaint = $this->complaints->create(
            $request->validated(),
            $request->user(),
        );

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update complaint status.
     *
     * Only admins can transition status. Enforces the state machine:
     * open → in_progress → resolved | rejected.
     */
    public function update(UpdateComplaintStatusRequest $request, Complaint $complaint): ComplaintResource
    {
        Gate::authorize('update', $complaint);

        $updated = $this->complaints->transitionStatus($complaint, $request->status());

        return new ComplaintResource($updated);
    }

    /**
     * Delete a complaint.
     *
     * Citizens can only delete their own open complaints. Admins can delete any.
     */
    public function destroy(Complaint $complaint): Response
    {
        Gate::authorize('delete', $complaint);

        $complaint->delete();

        return response()->noContent();
    }

    /**
     * Admin assigns a complaint to themselves.
     */
    public function assign(Request $request, Complaint $complaint): ComplaintResource
    {
        Gate::authorize('update', $complaint);

        $complaint->update(['assigned_to' => $request->user()->id]);

        return new ComplaintResource($complaint->load(['user', 'assignee']));
    }

    /**
     * View the audit trail for a complaint.
     *
     * Shows who did what and when — every create, status change, assignment, and delete is logged.
     */
    public function activity(Complaint $complaint): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        Gate::authorize('view', $complaint);

        $activities = $complaint->activities()
            ->with('user')
            ->latest()
            ->paginate(perPage: 20);

        return ActivityResource::collection($activities);
    }
}
