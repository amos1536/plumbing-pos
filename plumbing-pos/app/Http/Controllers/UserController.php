<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function writeAuditLog(
        Request $request,
        string $action,
        User $user,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'module' => 'User Management',
            'description' => $action . ' user account: '
                . $user->name
                . ' (' . $user->email . ')',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function index()
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'cashier']),
            ],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->save();

        $this->writeAuditLog(
            $request,
            'Created',
            $user,
            null,
            $user->only(['name', 'email', 'role'])
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'cashier']),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        // Get the currently authenticated user.
        $currentUser = $request->user();

        // Prevent an administrator from removing their own admin role.
        if (
            $currentUser &&
            (int) $currentUser->getAuthIdentifier() ===
                (int) $user->getAuthIdentifier() &&
            $validated['role'] !== 'admin'
        ) {
            return back()
                ->withErrors([
                    'role' => 'You cannot remove your own administrator role.',
                ])
                ->withInput();
        }

        // Prevent removing the last administrator.
        if (
            $user->role === 'admin' &&
            $validated['role'] !== 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return back()
                ->withErrors([
                    'role' => 'The system must have at least one administrator.',
                ])
                ->withInput();
        }

        // Capture the existing details before making changes.
        $oldValues = $user->only(['name', 'email', 'role']);

        // Update the user's details.
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Change the password only when a new one is provided.
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Record the update without storing any password.
        $this->writeAuditLog(
            $request,
            'Updated',
            $user,
            $oldValues,
            $user->only(['name', 'email', 'role'])
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User account updated successfully.');
    }
}
