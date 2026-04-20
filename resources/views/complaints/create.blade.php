@extends('layouts.app')

@section('content')
    <div class="card" style="max-width: 640px;">
        <h2>File a Complaint</h2>

        <form method="POST" action="/complaints" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Brief summary of the issue" required>
                @error('title') <small style="color:#e94560;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Describe the issue in detail..." required>{{ old('description') }}</textarea>
                @error('description') <small style="color:#e94560;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="attachment">Attachment (optional)</label>
                <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                <small style="color:#888;">Max 5MB. Allowed: jpeg, png, gif, pdf, doc, docx</small>
                @error('attachment') <small style="color:#e94560;">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Submit Complaint</button>
            <a href="/dashboard" style="margin-left: 12px; color: #666; text-decoration: none;">Cancel</a>
        </form>
    </div>
@endsection
