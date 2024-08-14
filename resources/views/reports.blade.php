@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card" id="reportCriteria">
        <div class="card-body">
            <div class="col-md-4">
                <h3 class="card-title mt-0 mb-0">Enter your Report Criteria</h3>
            </div>
            <form method="GET" action="{{ route('reports')}}">
                <div class="row d-flex">
                    <div class="col-md-3 text-center">
                        <div class="card mt-1">
                            <div class="card-body bg-dark text-light rounded">
                                <h5 class="card-title">Start Week </h5>

                                <fieldset>
                                    <label for="datePickerStarting" Date:>
                                        <input id="datePickerStarting" type="date" min="2024-06-30" name="start_date"
                                            value="{{request()->start_date}}" class="form-control">
                                    </label>
                                </fieldset>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 text-center">
                        <div class="card mt-1">
                            <div class="card-body bg-dark text-light rounded">
                                <h5 class="card-title">End Week </h5>

                                <fieldset>
                                    <label for="datePickerEnding" Date:>
                                        <input id="datePickerEnding" type="date" min="2024-06-30" name="end_date"
                                            value="{{request()->end_date}}" class="form-control">
                                    </label>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 text-center">
                        <div class="card mt-1">
                            <div class="card-body bg-dark text-light rounded">
                                <h5 class="card-title">Select Regions</h5>
                                <label for="">
                                    (cntrl-click to select multiple cntrl-a to select all)
                                </label>
                                <fieldset>
                                    <select class="selectpicker form-control" name="regions[]" multiple>
                                        @foreach($all_regions as $region)

                                        <option value="{{$region}}" {{in_array($region,request()->regions ?? []) ?
                                            'Selected'
                                            : ''}}>{{$region}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 text-center">
                        <div class="card mt-1">
                            <div class="card-body bg-dark text-light rounded">
                                <fieldset>
                                    <a href="{{route('reports')}}" class="btn btn-sm btn-primary form-control mb-2"
                                        type="submit" name="clear">Clear Filters</a>
                                    <button class="btn btn-sm btn-primary form-control" type="submit">Submit</button>
                                </fieldset>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center fw-bold">Approval Status</h2>
        </div>
        <div class="card-body">

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">

                            <thead class="table table-dark sticky-top">

                                <th width="5%">Submit</th>
                                <th width="5%">Approved Status</th>
                                <th width="5%">Published Status</th>
                                <th>Region</th>
                                <th>Unit No</th>
                                <th width="10%">Schedule Date</th>
                                <th>Forecast Sales</th>
                                <th>vs 6wk Avg</th>
                                <th>vs 3wk Avg</th>
                                <th>vs Last Year</th>
                                <th>Labor Pct</th>
                                <th>Labor hrs +-/</th>
                                <th>OT Hrs</th>
                                <th>-</th>
                                <th width="28%">Approved By</th>

                            </thead>

                            <tbody>

                                @foreach($reports as $report )
                                <tr>


                                    <td class="align-middle">
                                        @if($report->Published)
                                        <button class="btn btn-sm" style="background-color: grey">Published</button>
                                        @else
                                        <form action="{{ route('report.toggle', $report->ID)}}" method="POST">
                                            @csrf
                                            @method('put')
                                            <button
                                                class="btn {{$report->Approved ? 'btn-danger'  :'btn-primary'}} btn-sm"
                                                type="submit">{{$report->Approved ? 'Revoke' :'Approve'}}</button>
                                        </form>
                                        @endif
                                    </td>
                                    <td align="center"
                                        style="text-align:center; font-size:150%; font-weight:bold; color:green;">
                                        @if($report->Approved) &#10004; @endif
                                    </td>
                                    <td align="center"
                                        style="text-align:center; font-size:150%; font-weight:bold; color:green;">
                                        @if($report->Published) &#10004; @endif
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
                                    <td class="align-middle">
                                        <a
                                            href="{{ route('download.pdf', ['unit' => $report->UnitNo, 'filename' => 'Schedule-' . $report->UnitNo . '-Weekof-' . $report->ScheduleDate . '.pdf']) }}"
                                            target="_blank" style="text-decoration:underline;cursor: pointer">
                                            <i class="fa fa-file-pdf-o items-center" style="font-size:20px;"></i>
                                        </a>
                                    </td>
                                    <td class="align-middle">{{$report->ApprovedBy}}</td>

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
@endsection
