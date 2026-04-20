@extends('layouts.app')

@section('content')
    <a href="/dashboard" style="color: #0077b6; text-decoration: none; font-size: 14px;">← Back to Dashboard</a>

    <div class="card" style="margin-top: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <h2>{{ $complaint->title }}</h2>
            <span class="badge badge-{{ $complaint->status->value }}">{{ $complaint->status->value }}</span>
        </div>

        <dl class="detail-grid" style="margin-top: 16px;">
            <dt>Complaint ID</dt>
            <dd>#{{ $complaint->id }}</dd>

            <dt>Filed By</dt>
            <dd>{{ $complaint->user->name }} ({{ $complaint->user->email }})</dd>

            <dt>Assigned To</dt>
            <dd>{{ $complaint->assignee?->name ?? 'Unassigned' }}</dd>

            <dt>Filed On</dt>
            <dd>{{ $complaint->created_at->format('d M Y, h:i A') }}</dd>

            <dt>Last Updated</dt>
            <dd>{{ $complaint->updated_at->format('d M Y, h:i A') }}</dd>
        </dl>

        <div class="section-title">Description</div>
        <p style="font-size: 14px; line-height: 1.6; color: #333;">{{ $complaint->description }}</p>
    </div>

    {{-- Admin actions: status change + assign --}}
    @if (auth()->user()->isAdmin())
        <div class="card">
            <h2>Admin Actions</h2>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                {{-- Status change --}}
                <form method="POST" action="/complaints/{{ $complaint->id }}/status" style="flex: 1; min-width: 200px;">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label>Change Status</label>
                        <select name="status">
                            @foreach (\App\Enums\ComplaintStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ $complaint->status === $status ? 'selected' : '' }}>
                                    {{ $status->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                </form>

                {{-- Assign --}}
                <form method="POST" action="/complaints/{{ $complaint->id }}/assign" style="flex: 1; min-width: 200px;">
                    @csrf
                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="admin_id">
                            <option value="">— Select Admin —</option>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $complaint->assigned_to === $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                </form>
            </div>
        </div>
    @endif

    {{-- Attachments --}}
    <div class="card">
        <div class="section-title" style="margin-top: 0;">Attachments ({{ $complaint->attachments->count() }})</div>

        @forelse ($complaint->attachments as $attachment)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                <div>
                    <a href="/attachments/{{ $attachment->id }}" target="_blank" style="color: #0077b6; text-decoration: none; font-weight: 500;">
                        {{ $attachment->filename }}
                    </a>
                    <span style="color: #888; font-size: 12px; margin-left: 8px;">
                        {{ number_format($attachment->size / 1024, 1) }} KB &middot; {{ $attachment->mime_type }}
                    </span>
                </div>
                <span style="color: #aaa; font-size: 12px;">by {{ $attachment->user->name ?? 'Unknown' }}</span>
            </div>
        @empty
            <p style="color: #888; font-size: 14px;">No attachments.</p>
        @endforelse

        {{-- Upload form --}}
        <form method="POST" action="/complaints/{{ $complaint->id }}/upload" enctype="multipart/form-data" style="margin-top: 16px; display: flex; align-items: end; gap: 12px;">
            @csrf
            <div class="form-group" style="margin-bottom: 0; flex: 1;">
                <label for="file">Upload attachment</label>
                <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="margin-bottom: 1px;">Upload</button>
        </form>
    </div>

    {{-- Comments --}}
    <div class="card">
        <div class="section-title" style="margin-top: 0;">Comments ({{ $complaint->comments->count() }})</div>

        @forelse ($complaint->comments as $comment)
            <div class="comment">
                <div class="comment-meta">
                    <strong>{{ $comment->user->name }}</strong>
                    <span class="badge badge-{{ $comment->user->isAdmin() ? 'in_progress' : 'open' }}" style="font-size: 10px;">
                        {{ $comment->user->role->value }}
                    </span>
                    &middot; {{ $comment->created_at->diffForHumans() }}
                </div>
                <div class="comment-body">{{ $comment->body }}</div>
            </div>
        @empty
            <p style="color: #888; font-size: 14px;">No comments yet.</p>
        @endforelse

        {{-- Add comment form --}}
        <form method="POST" action="/complaints/{{ $complaint->id }}/comment" style="margin-top: 16px;">
            @csrf
            <div class="form-group">
                <label for="body">Add a comment</label>
                <textarea id="body" name="body" rows="3" placeholder="Write your comment..." required></textarea>
                @error('body') <small style="color:#e94560;">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
        </form>
    </div>

    {{-- Activity log --}}
    <div class="card">
        <div class="section-title" style="margin-top: 0;">Activity Log</div>

        @forelse ($complaint->activities->sortByDesc('created_at') as $activity)
            <div style="padding: 6px 0; font-size: 13px; border-bottom: 1px solid #f5f5f5;">
                <strong>{{ $activity->user?->name ?? 'System' }}</strong>
                <span style="color: #0077b6;">{{ $activity->action }}</span>
                @if ($activity->changes)
                    @php $changes = $activity->changes; @endphp
                    @if (isset($changes['old']) && isset($changes['new']))
                        —
                        @foreach ($changes['new'] as $field => $newVal)
                            <code>{{ $field }}</code>: {{ $changes['old'][$field] ?? 'null' }} → <strong>{{ $newVal }}</strong>
                        @endforeach
                    @endif
                @endif
                <span style="color: #aaa; margin-left: 8px;">{{ $activity->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <p style="color: #888; font-size: 14px;">No activity recorded.</p>
        @endforelse
    </div>
@endsection
