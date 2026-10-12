<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AdminActivityLog::with('admin')
            ->when($request->admin_id, fn ($q, $id) => $q->where('admin_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where('activity', 'like', "%{$s}%"))
            ->latest('created_at')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'admins' => User::internal()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
