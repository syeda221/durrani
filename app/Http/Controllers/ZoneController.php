<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::orderByDesc('id')->get();
        return view('admin_panel.zone.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone' => 'required|string|max:255'
        ]);

        if ($request->filled('edit_id')) {
            $zone = Zone::findOrFail($request->edit_id);
            $zone->update([
                'zone' => trim($request->zone)
            ]);
            $message = 'Zone updated successfully.';
        } else {
            $zone = Zone::create([
                'zone' => trim($request->zone)
            ]);
            $message = 'Zone created successfully.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'zone' => $zone
            ]);
        }

        return redirect()->route('zone.index')->with('success', $message);
    }

    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'deleted',
                'message' => 'Zone deleted successfully.'
            ]);
        }

        return redirect()->route('zone.index')->with('success', 'Zone deleted successfully.');
    }
}
