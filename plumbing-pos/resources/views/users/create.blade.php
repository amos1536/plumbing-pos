
@extends('layouts.app')

@section('content')
<div class="container">
    <div style="margin-bottom:20px;">
        <h2>Add New User</h2>
        <p>Create an account for a system administrator or cashier.</p>
    </div>

    @if($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:12px; margin-bottom:15px; border-radius:5px;">
            <ul style="margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background:white; padding:25px; border-radius:8px; max-width:700px;">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:18px;">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:5px;"
                >
            </div>

            <div style="margin-bottom:18px;">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:5px;"
                >
            </div>

            <div style="margin-bottom:18px;">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:5px;"
                >
                <small>Use at least 8 characters.</small>
            </div>

            <div style="margin-bottom:18px;">
                <label for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:5px;"
                >
            </div>

            <div style="margin-bottom:22px;">
                <label for="role">User Role</label>
                <select
                    id="role"
                    name="role"
                    required
                    style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:5px;"
                >
                    <option value="">Select a role</option>
                    <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>
                        Cashier
                    </option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                        Administrator
                    </option>
                </select>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button
                    type="submit"
                    style="background:#2563eb; color:white; border:none; padding:11px 18px; border-radius:5px; cursor:pointer;"
                >
                    Create User
                </button>

                <a
                    href="{{ route('users.index') }}"
                    style="background:#e5e7eb; color:#111827; padding:11px 18px; border-radius:5px; text-decoration:none;"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection