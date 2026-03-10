<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model_type', 'like', "%{$request->model}%");
        }

        $logs = $query->paginate(25);
        
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'super-admin']);
        })
        ->orWhere('is_admin', true)
        ->get();

        return view('admin.activity-logs.index', compact('logs', 'users'));
    }
}
