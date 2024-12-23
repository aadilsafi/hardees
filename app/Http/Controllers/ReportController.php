<?php

namespace App\Http\Controllers;

use App\Models\ScheduleApproval;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;

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
            ->orderBy('tblStores.region', 'desc') // First order by region
            ->orderBy('tblScheduleApproval.ScheduleDate', 'desc')
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
            'ApprovedBy' => $report->Approved ? '' : auth()->user()->name . ' @ ' . now()->format('Y-m-d H:i:s'),
            'Comments' => !$report->Approved ? '' : $report->Comments,
        ]);
        if (!$report->Approved) {
            session()->flash('comment_modal', true);
            session()->flash('comment_modal_comment', $report->Comments);
            session()->flash('comment_modal_id', $report->ID);
        }
        if ($report->Approved) {
            $message = 'The schedule for Week of ' . $report->ScheduleDate . ' has been approved';
            $subject = 'Schedule Approved';
            $this->sendEmail($message, $subject, $report->store?->EmailAddress);
        }
        return redirect()->back()->with('success', $report->Approved ? 'Schedule was approved Store will be notified!' : 'Schedule was revoked Store will be notified!');
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
            $stores = Store::with('scheduleApprovals')->when($regions, function ($query, $regions) {
                return $query->whereIn('Region', $regions);
            })->get();

            foreach ($stores as $store) {
            if(count($store?->scheduleApprovals) <= 0) continue;

            $min_schedule = $store?->scheduleApprovals?->min('ScheduleDate');
            // If the minimum schedule date is greater than the previous week, then we don't need to check for missing files.
            if ($min_schedule > $previous_week) {
                continue;
            }
                $unit_no = $store->StoreNumber;
                $unit_dir = $baseDir . '/' . $unit_no;

                if (File::exists($unit_dir)) {
                    $expectedFile = "Schedule-{$unit_no}-WeekOf-{$previous_week}.pdf";
                    $on_db = ScheduleApproval::where('ScheduleName', $expectedFile)->exists();

                    if (!File::exists($unit_dir . '/' . $expectedFile)) {
                        $missing_files[] = [
                            'unit_no' => $unit_no,
                            'week' => $previous_week,
                            'missing_file' => $expectedFile,
                            'region'       => $store->Region,
                            'on_db'        => $on_db
                        ];
                    }
                }
            }
        }
        return $missing_files;
    }
    public function closeModalSession(Request $request)
    {
        session()->forget('show_modal');
        return response()->json(['status' => 'success']);
    }
    public function addComment(Request $request)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'id' => 'required|exists:tblScheduleApproval,ID',
        ]);
        $schedule = ScheduleApproval::findOrFail($request->id);

        $comment = trim($request->input('comment'));
        $comment = str_replace(["\r\n", "\r"], "\n", $comment);

        $comment = preg_replace('/\n+/', "\n", $comment);

        $comment = mb_strcut($comment, 0, 255, 'UTF-8');
        if (!$request->cancel) {
            $schedule->update([
                'Comments' => $comment
            ]);
        }
        if ($request->cancel) {
            $message = "The schedule for Week of " . $schedule->ScheduleDate . " has been revoked";
            // $message .= $schedule->Comments ? "\nThe comment reads: " . $schedule->Comments : "";
            $subject = "Schedule Revoked";
            $this->sendEmail($message, $subject, $schedule->store?->EmailAddress);
            return \redirect()->back();
        } elseif ($request->is_revoke) {
            $message = "The schedule for Week of " . $schedule->ScheduleDate . " has been revoked";
            $message .= $comment ? "\nThe comment reads: " . $comment : "";
            $subject = "Schedule Revoked";
            $this->sendEmail($message, $subject, $schedule->store?->EmailAddress);
            return redirect()->back()->with('success', 'Comment added successfully Store will be notified!');
        }
        $message = "A comment has been submitted for Schedule for Week " . $schedule->ScheduleDate . "\n";
        $message .= $comment ? "\nThe comment reads: " . $comment : "";
        $subject = "Schedule Comment Added";
        $this->sendEmail($message, $subject, $schedule->store?->EmailAddress);

        return redirect()->back()->with('success', 'Comment added successfully Store will be notified!');
    }
    public function sendEmail($textMessage, $subject, $email)
    {
        if (!$email) {
            return;
        }
        Mail::raw($textMessage, function ($message) use ($email, $subject) {
            $message->to($email)
                ->subject($subject)
                ->replyTo(auth()->user()?->email);
        });
    }
    public function revokeMail(Request $request)
    {
        $report = ScheduleApproval::findOrFail($request->id);
        $message = 'The schedule for Week of ' . $report->ScheduleDate . ' has been revoked';
        $message .= $report->Comments ? '\nThe comment reads: ' . $report->Comments : '';
        $subject = 'Schedule Revoked';
        $this->sendEmail($message, $subject, $report->store?->EmailAddress);
    }
}
