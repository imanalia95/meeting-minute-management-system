<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::query()
            ->when($request->filled('status'), fn ($q) => $q->status($request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->orderByDesc('meeting_date')
            ->paginate(10)
            ->withQueryString();

        return view('history.index', compact('meetings'));
    }
}
