@extends('layouts.auth')

@section('content')
<div style="width:100%; max-width:400px; padding:0 1.5rem;">

    <div class="text-center mb-10">
        <p class="font-display" style="font-size:1.5rem; color:#C9B99A; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.5rem;">
            Sapiens House
        </p>
        <p style="color:#3A3A35; font-size:0.7rem; letter-spacing:0.12em; text-transform:uppercase;">
            Admin Panel
        </p>
    </div>

    <div style="border:1px solid #2E2E2A; padding:2.5rem; background-color:#0F0F0D;">
        <form method="POST" action="/login">
            @csrf

            @if($errors->any())
            <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25); color:#ef4444; padding:0.75rem 1rem; font-size:0.8rem; margin-bottom:1.5rem;">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="mb-5">
                <label style="display:block; color:#8C7E6A; font-size:0.7rem; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.5rem;">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       autofocus class="form-input">
            </div>

            <div class="mb-6">
                <label style="display:block; color:#8C7E6A; font-size:0.7rem; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.5rem;">
                    Password
                </label>
                <input type="password" name="password" required class="form-input">
            </div>

            <div class="flex items-center gap-2 mb-6">
                <input type="checkbox" name="remember" id="remember"
                       style="accent-color:#B8925A; width:14px; height:14px;">
                <label for="remember" style="color:#8C7E6A; font-size:0.8rem;">Remember me</label>
            </div>

            <button type="submit" class="btn-gold" style="width:100%; text-align:center; padding:0.875rem;">
                Sign In
            </button>
        </form>
    </div>

    <p class="text-center mt-6">
        <a href="{{ route('home') }}" style="color:#3A3A35; font-size:0.75rem; letter-spacing:0.05em;" class="hover:underline">
            ← Back to Website
        </a>
    </p>
</div>
@endsection
