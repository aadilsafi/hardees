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
        $all_regions = Store::distinct()
            ->pluck('Region')
            ->sort()
            ->values();

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
            ->get();

        return view('reports', compact('reports', 'all_regions'));
    }

    public function pendingReports()
    {
        $regions = explode(',', auth()->user()->regions ?? []);
        $reports = ScheduleApproval::with('store')
            ->whereHas('store', function ($query) use ($regions) {
                $query->whereIn('Region', $regions);
            })
            ->where('Approved', 0)
            ->join('tblstores', 'tblscheduleapproval.UnitNo', '=', 'tblstores.StoreNumber')
            ->select('tblscheduleapproval.*', 'tblstores.Region as store_region', 'tblstores.StoreNumber as store_number')
            ->orderBy('store_region', 'asc')
            ->orderBy('store_number', 'asc')
            ->orderBy('tblscheduleapproval.ScheduleDate', 'asc')
            ->get();

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
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
