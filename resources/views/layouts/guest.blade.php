<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Grievance Portal' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; min-height: 100vh; display: flex; }

        /* ── Left panel (branding) ──────────────────── */
        .left-panel {
            width: 45%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(233, 69, 96, 0.08);
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(0, 119, 182, 0.1);
        }
        .left-brand { position: relative; z-index: 1; }
        .left-brand .icon { width: 56px; height: 56px; background: #e94560; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 800; margin-bottom: 28px; }
        .left-brand h1 { font-size: 32px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.5px; }
        .left-brand p { font-size: 16px; line-height: 1.6; opacity: 0.8; max-width: 340px; }
        .left-features { position: relative; z-index: 1; margin-top: 48px; }
        .feature { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 20px; }
        .feature-dot { width: 8px; height: 8px; border-radius: 50%; background: #e94560; margin-top: 6px; flex-shrink: 0; }
        .feature-text { font-size: 14px; opacity: 0.75; line-height: 1.5; }
        .feature-text strong { opacity: 1; display: block; margin-bottom: 2px; }

        /* ── Right panel (form) ─────────────────────── */
        .right-panel {
            width: 55%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f8fa;
            padding: 40px;
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
        }
        .auth-card h2 { font-size: 26px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .auth-card .subtitle { font-size: 14px; color: #888; margin-bottom: 28px; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus { outline: none; border-color: #0077b6; box-shadow: 0 0 0 3px rgba(0,119,182,0.1); }
        .form-group input::placeholder { color: #bbb; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #0077b6, #005f8d);
            color: white;
            transition: transform 0.1s, box-shadow 0.2s;
            margin-top: 4px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,119,182,0.3); }
        .btn-submit:active { transform: translateY(0); }

        .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #888; }
        .auth-footer a { color: #0077b6; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        .validation-errors { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; }
        .validation-errors ul { margin: 0; padding-left: 16px; }
        .validation-errors li { color: #991b1b; font-size: 13px; margin-bottom: 3px; }

        .demo-creds { background: #f0f7ff; border: 1px solid #d0e7ff; border-radius: 8px; padding: 14px 16px; margin-top: 24px; }
        .demo-creds p { font-size: 12px; color: #0077b6; font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .demo-creds table { width: 100%; font-size: 13px; }
        .demo-creds td { padding: 3px 0; color: #444; }
        .demo-creds td:first-child { font-weight: 600; width: 60px; }
        .demo-creds code { background: #e8f0fe; padding: 1px 6px; border-radius: 4px; font-size: 12px; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; padding: 40px 30px; min-height: auto; }
            .right-panel { width: 100%; padding: 30px 20px; }
            .left-features { display: none; }
        }
    </style>
</head>
<body>
    <div class="left-panel">
        <div class="left-brand">
            <div class="icon">G</div>
            <h1>Grievance Portal</h1>
            <p>A platform for citizens to file complaints and track resolutions from government departments.</p>
        </div>
        <div class="left-features">
            <div class="feature">
                <div class="feature-dot"></div>
                <div class="feature-text">
                    <strong>File Complaints</strong>
                    Submit issues with descriptions and photo evidence
                </div>
            </div>
            <div class="feature">
                <div class="feature-dot"></div>
                <div class="feature-text">
                    <strong>Track Status</strong>
                    Real-time updates as your complaint moves through the system
                </div>
            </div>
            <div class="feature">
                <div class="feature-dot"></div>
                <div class="feature-text">
                    <strong>Get Notified</strong>
                    Email notifications when status changes
                </div>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="auth-card">
            @if ($errors->any())
                <div class="validation-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

            <div class="demo-creds">
                <p>Demo Credentials</p>
                <table>
                    <tr><td>Admin</td><td><code>admin@example.com</code></td><td><code>password</code></td></tr>
                    <tr><td>Citizen</td><td><code>citizen@example.com</code></td><td><code>password</code></td></tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
