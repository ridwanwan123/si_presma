<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Activity::query()
            ->with([
                'causer:id,nama,email',
                'subject',
            ]);

        // =========================
        // ROLE BASE ACCESS CONTROL
        // =========================
        if ($user->role->nama !== 'Administrator') {
            // madrasah hanya lihat dirinya sendiri
            $query->where('causer_id', $user->id);
        }

        // =========================
        // FILTER EVENT
        // =========================
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // =========================
        // FILTER SEARCH
        // =========================
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // =========================
        // FILTER DATE
        // =========================
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // =========================
        // USERS FILTER (IMPORTANT)
        // =========================
        $usersQuery = Activity::query()
            ->with('causer:id,nama')
            ->whereNotNull('causer_id');

        if ($user->role->nama !== 'Administrator') {
            $usersQuery->where('causer_id', $user->id);
        }

        $users = $usersQuery
            ->get()
            ->pluck('causer')
            ->filter()
            ->unique('id')
            ->sortBy('nama')
            ->values();

        $breadcrumb = breadcrumb([
            'Data Activity'
        ]);

        return view('activity.index', compact(
            'activities',
            'users',
            'breadcrumb'
        ));
    }
}