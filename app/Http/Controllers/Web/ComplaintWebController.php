<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ComplaintWebController extends Controller
{
    public function __construct(private readonly ComplaintService $complaints)
    {
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $query = Complaint::with(['user', 'assignee'])->latest();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $baseQuery = $user->isAdmin() ? Complaint::query() : Complaint::where('user_id', $user->id);

        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'open'        => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved'    => (clone $baseQuery)->where('status', 'resolved')->count(),
            'rejected'    => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return view('dashboard', [
            'complaints' => $query->paginate(15),
            'stats'      => $stats,
        ]);
    }

    public function create(Request $request)
    {
        if ($request->user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Admins cannot file complaints.');
        }

        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'min:5', 'max:120'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'attachment'  => ['nullable', 'file', 'max:5120', 'mimes:jpeg,png,gif,pdf,doc,docx'],
        ]);

        $complaint = $this->complaints->create(
            ['title' => $data['title'], 'description' => $data['description']],
            $request->user(),
        );

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("complaints/{$complaint->id}", 'local');

            $complaint->attachments()->create([
                'user_id'   => $request->user()->id,
                'filename'  => $file->getClientOriginalName(),
                'path'      => $path,
                'mime_type' => $file->getMimeType(),
                'size'      => $file->getSize(),
            ]);
        }

        return redirect("/complaints/{$complaint->id}")
            ->with('success', 'Complaint filed successfully.');
    }

    public function show(Complaint $complaint)
    {
        Gate::authorize('view', $complaint);

        $complaint->load(['user', 'assignee', 'comments.user', 'attachments.user', 'activities.user']);

        $admins = User::where('role', UserRole::Admin)->get();

        return view('complaints.show', compact('complaint', 'admins'));
    }

    public function comment(Request $request, Complaint $complaint)
    {
        Gate::authorize('view', $complaint);

        $request->validate(['body' => ['required', 'string', 'min:2', 'max:5000']]);

        $complaint->comments()->create([
            'body'    => $request->input('body'),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Comment posted.');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        Gate::authorize('update', $complaint);

        $request->validate(['status' => ['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\ComplaintStatus::class)]]);

        $newStatus = \App\Enums\ComplaintStatus::from($request->input('status'));

        $this->complaints->transitionStatus($complaint, $newStatus);

        return back()->with('success', "Status updated to {$newStatus->value}.");
    }

    public function assign(Request $request, Complaint $complaint)
    {
        Gate::authorize('update', $complaint);

        $adminId = $request->input('admin_id');
        $admin = User::findOrFail($adminId);

        if (! $admin->isAdmin()) {
            return back()->with('error', 'Selected user is not an admin.');
        }

        $complaint->update(['assigned_to' => $admin->id]);

        return back()->with('success', "Assigned to {$admin->name}.");
    }

    public function downloadAttachment(Attachment $attachment)
    {
        Gate::authorize('view', $attachment->complaint);

        return response()->file(
            Storage::disk('local')->path($attachment->path),
            ['Content-Type' => $attachment->mime_type],
        );
    }

    public function uploadAttachment(Request $request, Complaint $complaint)
    {
        Gate::authorize('view', $complaint);

        $request->validate([
            'file' => ['required', 'file', 'max:5120', 'mimes:jpeg,png,gif,pdf,doc,docx'],
        ]);

        $file = $request->file('file');
        $path = $file->store("complaints/{$complaint->id}", 'local');

        $complaint->attachments()->create([
            'user_id'   => $request->user()->id,
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);

        return back()->with('success', 'File uploaded.');
    }
}
