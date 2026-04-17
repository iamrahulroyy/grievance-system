<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * List comments on a complaint.
     */
    public function index(Complaint $complaint): AnonymousResourceCollection
    {
        Gate::authorize('view', $complaint);

        $comments = $complaint->comments()
            ->with('user')
            ->oldest()
            ->paginate(perPage: 20);

        return CommentResource::collection($comments);
    }

    /**
     * Add a comment to a complaint.
     *
     * Both citizens (owner) and admins can comment.
     */
    public function store(StoreCommentRequest $request, Complaint $complaint): JsonResponse
    {
        Gate::authorize('view', $complaint);

        $comment = $complaint->comments()->create([
            'body'    => $request->validated('body'),
            'user_id' => $request->user()->id,
        ]);

        return (new CommentResource($comment->load('user')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
