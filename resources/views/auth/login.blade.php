@extends('layouts.guest')

@section('content')
    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your account</p>

    <form method="POST" action="/login">
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit">Sign in</button>
    </form>

    <p class="auth-footer">
        Don't have an account? <a href="/register">Create one</a>
    </p>
@endsection
