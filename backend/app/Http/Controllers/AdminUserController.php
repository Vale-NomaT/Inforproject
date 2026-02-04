<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderBy('created_at', 'desc');

        $type = $request->query('type');

        if ($type && in_array($type, ['parent', 'driver', 'admin'], true)) {
            $query->where('user_type', $type);
        }

        $users = $query->get();

        return view('admin.users', [
            'users' => $users,
            'filterType' => $type,
        ]);
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $user->status = 'suspended';
        $user->status_reason = $data['reason'] ?? null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('status', 'User suspended.');
    }
}
