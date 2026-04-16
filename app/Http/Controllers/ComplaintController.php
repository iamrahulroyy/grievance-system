<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ComplaintController extends Controller
{
    public function __construct(private readonly ComplaintService $complaints)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $complaints = Complaint::query()
            ->latest('id')
            ->paginate(perPage: 15);

        return ComplaintResource::collection($complaints);
    }

    public function show(Complaint $complaint): ComplaintResource
    {
        return new ComplaintResource($complaint);
    }

    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $complaint = $this->complaints->create($request->validated());

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateComplaintStatusRequest $request, Complaint $complaint): ComplaintResource
    {
        $updated = $this->complaints->transitionStatus($complaint, $request->status());

        return new ComplaintResource($updated);
    }

    public function destroy(Complaint $complaint): Response
    {
        $complaint->delete();

        return response()->noContent();
    }
}
