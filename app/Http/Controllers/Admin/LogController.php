<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(10);
          return view('admin.logs.index', compact('logs'));
    }
}
