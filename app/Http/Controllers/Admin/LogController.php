<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('admin');

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        if ($request->filled('acao')) {
            $query->where('acao', 'like', "%{$request->acao}%");
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $logs = $query->orderByDesc('created_at')->paginate(30);
        $admins = Admin::orderBy('nome')->get();

        return view('admin.logs.index', compact('logs', 'admins'));
    }

    public function show(ActivityLog $log)
    {
        $log->load('admin');
        return view('admin.logs.show', compact('log'));
    }
}
