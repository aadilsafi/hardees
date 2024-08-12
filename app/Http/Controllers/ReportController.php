<?php

namespace App\Http\Controllers;

use App\Models\ScheduleApproval;
use App\Models\Store;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reports(Request $request)
    {
        $regions = explode(',', auth()->user()->regions ?? []);
        $auth_user = auth()->user();
        if ($auth_user->role === 'super') {
            $all_regions = Store::distinct()
                ->pluck('Region')
                ->sort()
                ->values();
        } else {
            $all_regions = Store::distinct()
                ->whereIn('Region', $regions)
                ->pluck('Region')
                ->sort()
                ->values();
        }

        $reports = [];
        if (!$request->start_date && !$request->end_date && !$request->regions) {
            return view('reports', compact('all_regions', 'reports'));
        }
        $reports = ScheduleApproval::with('store')->whereHas('store', function ($query) use ($regions, $request) {
            $query->whereIn('region', $regions)
                ->when($request->regions, function ($query, $regions) {
                    return $query->whereIn('region', $regions);
                });
        })
            ->when($request->start_date, function ($query, $start_date) {
                return $query->where('ScheduleDate', '>=', $start_date);
            })
            ->when($request->end_date, function ($query, $end_date) {
                return $query->where('ScheduleDate', '<=', $end_date);
            })
            ->join('tblstores', 'tblscheduleapproval.UnitNo', '=', 'tblstores.StoreNumber')
            ->orderBy('tblstores.region', 'asc') // Add ordering by region here to affect the main query.
            ->select('tblscheduleapproval.*', 'tblstores.region as store_region') // Optionally select the region if needed for display or further logic.
            ->get();
        $reports = $reports->sortBy('store_region');
        return view('reports', compact('reports', 'all_regions'));
    }

    public function pendingReports()
    {
        $regions = explode(',', auth()->user()->regions ?? []);
        $auth_user = auth()->user();
        if ($auth_user->role === 'super') {
            $all_regions = Store::distinct()
                ->pluck('Region')
                ->sort()
                ->values();
        } else {
            $all_regions = Store::distinct()
                ->whereIn('Region', $regions)
                ->pluck('Region')
                ->sort()
                ->values();
        }
        $reports = ScheduleApproval::with('store')
            ->whereHas('store', function ($query) use ($regions) {
                $query->whereIn('Region', $regions);
            })
            ->where('Approved', 0)
            ->join('tblstores', 'tblscheduleapproval.UnitNo', '=', 'tblstores.StoreNumber')
            ->select('tblscheduleapproval.*', 'tblstores.Region as store_region', 'tblstores.StoreNumber as store_number')
            ->get();

        $reports = $reports->sortBy('store_region');
        return view('pending-reports', compact('reports'));
    }
    public function reportStatusToggle($id)
    {
        $report =  ScheduleApproval::findOrFail($id);
        $report->update([
            'Approved' => !$report->Approved,
            'ApprovedBy' => $report->Approved ? '' : auth()->user()->name . ' @ ' . now()->format('Y-m-d H:i:s')
        ]);
        return redirect()->back()->with('success', 'Report updated successfully.');
    }
    public function downloadPDF($unit, $filename)
    {
        $path = "SchedulerNet_SchedulePDFs/{$unit}/{$filename}";
        dd($path);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
