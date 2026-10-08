<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own account here.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', "User {$user->name} status updated.");
    }

    public function toggleRole(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own account here.');
        }

        $user->role = ($user->role === 'admin') ? 'customer' : 'admin';
        $user->save();

        return back()->with('success', "User {$user->name} role changed to {$user->role}.");
    }
}
