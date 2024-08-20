<?php

namespace App\Http\Controllers;

use App\Models\ScheduleApproval;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
            ->join('tblStores', 'tblScheduleApproval.UnitNo', '=', 'tblStores.StoreNumber')
            ->orderBy('tblStores.region', 'asc') // Add ordering by region here to affect the main query.
            ->select('tblScheduleApproval.*', 'tblStores.region as store_region') // Optionally select the region if needed for display or further logic.
            ->get();
        $reports = $reports->sortBy('store_region');
        return view('reports', compact('reports', 'all_regions'));
    }

    public function pendingReports()
    {
        $regions = explode(',', auth()->user()->regions ?? []);
        $missing_reports =  $this->checkMissingReports($regions);
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
            ->join('tblStores', 'tblScheduleApproval.UnitNo', '=', 'tblStores.StoreNumber')
            ->select('tblScheduleApproval.*', 'tblStores.Region as store_region', 'tblStores.StoreNumber as store_number')
            ->get();

        $reports = $reports->sortBy('store_region');
        return view('pending-reports', compact('reports', 'missing_reports'));
    }
    public function reportStatusToggle($id)
    {
        $report =  ScheduleApproval::findOrFail($id);
        $report->update([
            'Approved' => !$report->Approved,
            'ApprovedBy' => $report->Approved ? '' : auth()->user()->name . ' @ ' . now()->format('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', $report->Approved ? 'Schedule was approved.' : 'Schedule was revoked');
    }
    public function downloadPDF($unit, $filename)
    {
        $path = "SchedulerNet_SchedulePDFs/{$unit}/{$filename}";
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function checkMissingReports($regions = [])
    {
        $baseDir = 'SchedulerNet_SchedulePDFs';
        $missing_files = [];
        $start_day = \config('app.start_day');
        $now = Carbon::now();
        if ($now->dayOfWeek() >= $start_day) {
            $previous_week = $now->startOfWeek($start_day)->format('Y-m-d');
            $stores = Store::when($regions, function ($query, $regions) {
                return $query->whereIn('Region', $regions);
            })->get();

            foreach ($stores as $store) {
                $unit_no = $store->StoreNumber;
                $unit_dir = $baseDir . '/' . $unit_no;

                if (File::exists($unit_dir)) {
                    $expectedFile = "Schedule-{$unit_no}-WeekOf-{$previous_week}.pdf";
                    if (!File::exists($unit_dir . '/' . $expectedFile)) {
                        $missing_files[] = [
                            'unit_no' => $unit_no,
                            'week' => $previous_week,
                            'missing_file' => $expectedFile,
                            'region'       => $store->Region,
                        ];
                    }
                }
            }
        }
        return $missing_files;
    }
}
