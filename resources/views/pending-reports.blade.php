@extends('layouts.app')

@section('content')
<div class="container">
    <div class="modal modal-lg fade" id="missing_reportsModal" tabindex="-1" role="dialog" aria-labelledby="missing_reportsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="w-100 display-6 text-center fw-bold" id="missing_reportsModalLabel">Missing Schedule Reports</h3>
                </div>
                <div class="modal-body px-5" style="max-height: 50vh; overflow-y: auto;">
                    @if(count($missing_reports) > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Unit No</th>
                                <th>Week</th>
                                <th>Missing File</th>
                                <th>Region</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($missing_reports as $report)
                            <tr>
                                <td>{{ $report['unit_no'] }}</td>
                                <td>{{ $report['week'] }}</td>
                                <td>{{ $report['missing_file'] }}</td>
                                <td>{{ $report['region']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>No missing reports for the previous week.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center fw-bold">Schedules - Pending Approval</h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">

                            <thead class="table table-dark sticky-top">

                                <th width="5%">Status</th>

                                <th>Region</th>
                                <th>Unit No</th>
                                <th width="10%">Schedule Date</th>
                                <th>Forecast Sales</th>
                                <th>vs 6wk Avg</th>
                                <th>vs 3wk Avg</th>
                                <th>vs Last Year</th>
                                <th>Labor Pct</th>
                                <th>Labor hrs +/-</th>
                                <th>OT Hrs</8h>
                                <th width="28%">Schedule Name</th>

                            </thead>

                            <tbody>

                                @foreach($reports as $report )
                                <tr>


                                    <td>
                                        <form action="{{ route('report.toggle', $report->ID)}}" method="POST">
                                            @csrf
                                            @method('put')
                                            <button
                                                class="btn {{$report->Approved ? 'btn-danger'  :'btn-primary'}} btn-sm"
                                                type="submit">{{$report->Approved ? 'Revoke' :'Approve'}}</button>
                                        </form>
                                    </td>
                                    </td>

                                    <td class="align-middle">
                                        {{$report->store?->Region}}
                                    </td>
                                    <td class="align-middle" name="unitNo">
                                        {{$report->UnitNo}}
                                    </td>
                                    <td class="align-middle" name="scheduleDate">
                                        {{$report->ScheduleDate}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->ForecastSales}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->vs6WkAvg}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->vs3WkAvg}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->vsLastYear}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->LaborPct}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->LaborHrsOverUnder}}
                                    </td>
                                    <td class="align-middle">
                                        {{$report->OvertimeHours}}
                                    </td>

                                    <!-- The random number at the end of the PDF is to force it to use a new version of the file and not load a cached version on page reload/load  -->
                                    <td class="align-middle">
                                        <a href="{{ route('download.pdf', ['unit' => $report->UnitNo, 'filename' => $report->ScheduleName]) }}"
                                            target="_blank" style="text-decoration:underline;cursor: pointer">
                                            {{$report->ScheduleName}}
                                        </a>
                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session('show_modal') && count($missing_reports) > 0)
<script>
    $(document).ready(function() {
        $('#missing_reportsModal').modal('show');
        $('#missing_reportsModal').on('hidden.bs.modal', function () {
                $.ajax({
                    url: "{{ route('close-modal-session') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                    }
                });
            });

    });
</script>
@endif
@endsection

