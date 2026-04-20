<!DOCTYPE html>
<html>
<head>
    <title>Grievance Portal</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #1a1a2e; }
        .stats { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { margin: 0; font-size: 24px; color: #e94560; }
        .stat-card p { margin: 4px 0 0; color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th { background: #1a1a2e; color: white; text-align: left; padding: 12px 15px; }
        td { padding: 10px 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f8f8; }
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-open { background: #fff3cd; color: #856404; }
        .badge-in_progress { background: #cce5ff; color: #004085; }
        .badge-resolved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .note { margin-top: 30px; padding: 15px; background: #e8f4f8; border-left: 4px solid #0077b6; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Grievance Portal - All Complaints</h1>

    <div class="stats">
        <div class="stat-card">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['open'] }}</h3>
            <p>Open</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['in_progress'] }}</h3>
            <p>In Progress</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['resolved'] }}</h3>
            <p>Resolved</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Filed By</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Filed On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->id }}</td>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ $complaint->user->name }}</td>
                    <td>{{ $complaint->assignee?->name ?? '—' }}</td>
                    <td><span class="badge badge-{{ $complaint->status->value }}">{{ $complaint->status->value }}</span></td>
                    <td>{{ $complaint->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No complaints found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $complaints->links() }}
    </div>

    <div class="note">
        <strong>MVC Demo:</strong> This page is rendered server-side using Blade (Laravel's template engine).
        The same <code>Complaint</code> model and relationships power both this HTML view and the JSON API at <code>/api/complaints</code>.
        <br><br>
        <strong>View file:</strong> <code>resources/views/complaints/index.blade.php</code><br>
        <strong>Route:</strong> <code>routes/web.php → GET /complaints</code><br>
        <strong>Same model:</strong> <code>app/Models/Complaint.php</code>
    </div>
</body>
</html>
