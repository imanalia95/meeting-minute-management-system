<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use Illuminate\Http\Request;

class TindakanController extends Controller
{
    public function index(Request $request)
    {
        $actionItems = ActionItem::with(['assignee', 'meeting'])
            ->when($request->filled('status'), fn ($q) => $q->status($request->status))
            ->orderBy('due_date')
            ->paginate(10)
            ->withQueryString();

        return view('tindakan.index', compact('actionItems'));
    }

    public function destroy(ActionItem $actionItem)
    {
        $actionItem->delete();

        return back()->with('success', 'Tindakan berjaya dipadam.');
    }
}