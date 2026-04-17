<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * Upload a file to a complaint.
     *
     * Accepts multipart/form-data with a `file` field.
     * Max 5 MB, allowed types: jpeg, png, gif, pdf, doc, docx.
     */
    public function store(Request $request, Complaint $complaint): JsonResponse
    {
        Gate::authorize('view', $complaint);

        $request->validate([
            'file' => ['required', 'file', 'max:5120', 'mimes:jpeg,png,gif,pdf,doc,docx'],
        ]);

        $file = $request->file('file');
        $path = $file->store("complaints/{$complaint->id}", 'local');

        $attachment = $complaint->attachments()->create([
            'user_id'   => $request->user()->id,
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);

        return (new AttachmentResource($attachment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Download an attachment.
     *
     * Streams the file to the client. Only accessible to complaint owner or admin.
     */
    public function show(Attachment $attachment): StreamedResponse
    {
        Gate::authorize('view', $attachment->complaint);

        return Storage::disk('local')->download(
            $attachment->path,
            $attachment->filename,
            ['Content-Type' => $attachment->mime_type],
        );
    }

    /**
     * Delete an attachment.
     */
    public function destroy(Attachment $attachment): Response
    {
        Gate::authorize('update', $attachment->complaint);

        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();

        return response()->noContent();
    }
}
