
@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2>User Management</h2>
            <p>Manage system administrators and cashiers.</p>
        </div>

        <a href="{{ route('users.create') }}"
           style="background:#2563eb; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
            + Add User
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7; color:#166534; padding:12px; margin-bottom:15px; border-radius:5px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:12px; margin-bottom:15px; border-radius:5px;">
            <ul style="margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="overflow-x:auto; background:white; border-radius:8px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#eff6ff; text-align:left;">
                    <th style="padding:12px;">#</th>
                    <th style="padding:12px;">Name</th>
                    <th style="padding:12px;">Email</th>
                    <th style="padding:12px;">Role</th>
                    <th style="padding:12px;">Date Created</th>
                    <th style="padding:12px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px;">{{ $users->firstItem() + $loop->index }}</td>
                        <td style="padding:12px;">{{ $user->name }}</td>
                        <td style="padding:12px;">{{ $user->email }}</td>
                        <td style="padding:12px;">
                            <span style="padding:4px 8px; border-radius:4px; background:{{ $user->role === 'admin' ? '#dbeafe' : '#f3f4f6' }};">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="padding:12px;">{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
                        <td style="padding:12px;">
                            <a href="{{ route('users.edit', $user) }}"
                               style="color:#2563eb; text-decoration:none;">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:20px; text-align:center;">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection