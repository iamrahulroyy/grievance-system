@extends('layouts.app')

@section('content')
    <h2 style="margin-bottom: 20px;">
        {{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'My Complaints' }}
    </h2>

    <div class="stats">
        <div class="stat">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total</p>
        </div>
        <div class="stat">
            <h3>{{ $stats['open'] }}</h3>
            <p>Open</p>
        </div>
        <div class="stat">
            <h3>{{ $stats['in_progress'] }}</h3>
            <p>In Progress</p>
        </div>
        <div class="stat">
            <h3>{{ $stats['resolved'] }}</h3>
            <p>Resolved</p>
        </div>
        <div class="stat">
            <h3>{{ $stats['rejected'] }}</h3>
            <p>Rejected</p>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="margin: 0;">Complaints</h2>
            @unless (auth()->user()->isAdmin())
                <a href="/complaints/create" class="btn btn-primary btn-sm">+ File Complaint</a>
            @endunless
        </div>

        @if ($complaints->count())
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        @if (auth()->user()->isAdmin())
                            <th>Filed By</th>
                            <th>Assigned To</th>
                        @endif
                        <th>Status</th>
                        <th>Filed On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->id }}</td>
                            <td><a href="/complaints/{{ $complaint->id }}">{{ $complaint->title }}</a></td>
                            @if (auth()->user()->isAdmin())
                                <td>{{ $complaint->user->name }}</td>
                                <td>{{ $complaint->assignee?->name ?? '—' }}</td>
                            @endif
                            <td><span class="badge badge-{{ $complaint->status->value }}">{{ $complaint->status->value }}</span></td>
                            <td>{{ $complaint->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 16px;">{{ $complaints->links() }}</div>
        @else
            <div class="empty">
                <p>No complaints yet.</p>
                <a href="/complaints/create" class="btn btn-primary" style="margin-top: 12px;">File your first complaint</a>
            </div>
        @endif
    </div>
@endsection
