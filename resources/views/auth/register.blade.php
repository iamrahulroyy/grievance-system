@extends('layouts.guest')

@section('content')
    <h2>Create account</h2>
    <p class="subtitle">Register as a citizen to file complaints</p>

    <form method="POST" action="/register">
        @csrf

        <div class="form-group">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ravi Kumar" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Min 8 chars, letters + numbers" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
        </div>

        <button type="submit" class="btn-submit">Create account</button>
    </form>

    <p class="auth-footer">
        Already have an account? <a href="/login">Sign in</a>
    </p>
@endsection
