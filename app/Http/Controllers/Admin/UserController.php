<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function admins()
    {
        $admins = User::where('is_admin', true)->paginate(10);
        return view('admin.users.admins', compact('admins'));
    }

    public function customers(Request $request)
    {
        $query = User::where('is_admin', false)->with('customer');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(10);
        return view('admin.users.customers', compact('customers'));
    }

    public function promote(User $user)
    {
        if (Gate::denies('add-admin', auth()->user())) {
            abort(403, 'Only the Super Admin can promote users.');
        }

        $user->update(['is_admin' => true]);

        return back()->with('success', 'User promoted to Admin successfully.');
    }

    public function demote(User $user)
    {
        if (Gate::denies('add-admin', auth()->user())) {
            abort(403, 'Only the Super Admin can demote users.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->update(['is_admin' => false]);

        return back()->with('success', 'Admin demoted to Customer successfully.');
    }
}
