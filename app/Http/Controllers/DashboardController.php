<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use App\Models\Meeting;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'belum_mula' => ActionItem::status('belum_mula')->count(),
            'dalam_proses' => ActionItem::status('dalam_proses')->count(),
            'selesai' => ActionItem::status('selesai')->count(),
            'tertangguh' => ActionItem::status('tertangguh')->count(),
        ];

        $recentMeetings = Meeting::orderByDesc('meeting_date')->take(8)->get();

        return view('dashboard', compact('stats', 'recentMeetings'));
    }
}