@extends('layouts.app')

@section('content')
<div class="container">
    <div class="modal modal-lg fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <input type="hidden" name="id" id="report-id">
                <div class="modal-body">

                    <div class="mb-3">
                        <p id="noteText"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-lg fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Leave a Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="commentForm" action="{{route('comment.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="report-id">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="commentText" class="form-label">Your Comment here</label>
                            <textarea class="form-control" id="commentText" rows="4" name="comment"
                                placeholder="Type your comment here..." maxlength="255"></textarea>
                            <div class="form-text" id="charCount">0/255 characters used</div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        @if(session('comment_modal'))
                        {{-- <input type="hidden" name="id" value="{{session('comment_modal_id')}}"> --}}
                        <input type="hidden" name="is_revoke" value="True">
                        <button type="submit" class="btn btn-secondary" name="cancel" value="cancel">Cancel</button>
                        @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        @endif
                        <button type="submit" class="btn btn-primary" id="submitCommentButton">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            <h2 class="display-6 text-center fw-bold">Schedule Status</h2>
        </div>
        <div class="card-body">

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">

                            <thead class="table table-dark sticky-top">

                                <th width="5%">Status</th>
                                <th width="5%">Approved Status</th>
                                <th>Region</th>
                                <th>Unit No</th>
                                <th width="10%">Schedule Date</th>
                                <th>Forecast Sales</th>
                                <th>vs 6wk Avg</th>
                                <th>vs 3wk Avg</th>
                                <th>vs Last Year</th>
                                <th>Labor Pct</th>
                                <th>Labor hrs +/-</th>
                                <th>OT Hrs</th>
                                <th>-</th>
                                <th>-</th>
                                <th>-</th>
                                <th width="28%">Approved By</th>

                            </thead>

                            <tbody>

                                @foreach($reports as $report )
                                <tr>


                                    <td class="align-middle">
                                        @if($report->Published)
                                        <button class="btn btn-sm text-light"
                                            style="background-color: grey">Published</button>
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
                                    <td @class(['align-middle', 'text-danger font-weight-bold'=> $report->LaborHrsOverUnder > 0
                                        ])>
                                        <div class="d-inline-flex">
                                            @if($report->LaborHrsOverUnder > 0) <span>+</span> @endif <span>
                                                {{number_format($report->LaborHrsOverUnder,2)}} </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        {{$report->OvertimeHours}}
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('download.pdf', ['unit' => $report->UnitNo, 'filename' => $report->ScheduleName]) }}"
                                            target="_blank" style="text-decoration:underline;cursor: pointer">
                                            <i class="fa fa-file-pdf-o items-center" style="font-size:20px;"></i>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        @if($report->Published || $report->Approved)
                                        <button type="button" class="btn">
                                            <i class="fa fa-comment" style="color:grey;font-size:20px;"></i>
                                        </button>
                                        @else
                                        <div class="position-relative d-inline-block">

                                            <button type="button" class="btn" data-bs-toggle="modal"
                                                data-bs-target="#commentsModal" data-comment="{{ $report->Comments }}"
                                                data-id="{{$report->ID}}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="comment to store">
                                                <i class="fa fa-comment" style="color:#0d6efd;font-size:20px;"></i>
                                                @if($report->Comments != '')
                                                <span class="indicator-dot"></span>
                                                @endif
                                            </button>
                                        </div>
                                        @endif

                                    </td>
                                    <td class="align-middle">

                                        <div class="position-relative d-inline-block">

                                            @if($report->NoteFromStore != '' && $report->NoteFromStore != null)
                                            <div class="position-relative d-inline-block">
                                                <button type="button" class="btn" data-bs-toggle="modal"
                                                    data-bs-target="#notesModal"
                                                    data-note="{{ $report->NoteFromStore }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="note sent from store">
                                                    <i class="fa fa-file-text"
                                                        style="color:#0d6efd;font-size:20px;"></i>
                                                    <span class="indicator-dot"></span>
                                                </button>
                                            </div>
                                            @else
                                            <button type="button" class="btn" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="note sent from store">
                                                <i class="fa fa-file-text" style="color:grey;font-size:20px;"></i>
                                            </button>
                                            @endif
                                        </div>
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
@if(session('comment_modal'))
<script>
    var commentsModal = new bootstrap.Modal(document.getElementById('commentsModal'));
    let g_comment = "{{session('comment_modal_comment')}}";
    let g_id = "{{session('comment_modal_id')}}";
    var commentModal = document.getElementById('commentsModal');
    var idInput = commentModal.querySelector('#report-id');
    idInput.value = g_id;
    var commentText = commentModal.querySelector('#commentText');
    commentText.value = g_comment;
    commentsModal.show();
</script>
@endif
<script>
    // Listen for the modal show event to set the comment
    var commentModal = document.getElementById('commentsModal');
    commentModal.addEventListener('show.bs.modal', function (event) {

        var button = event.relatedTarget;
        var comment =  button.getAttribute('data-comment');
        var id =  button.getAttribute('data-id');
        var idInput = commentModal.querySelector('#report-id');
        idInput.value = id;

        var commentText = commentModal.querySelector('#commentText');
        commentText.value = comment;

        // Trigger input event to update character count on modal open
        updateCharCount();
    });
    var noteModal = document.getElementById('notesModal');
    noteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var note = button.getAttribute('data-note');
        var noteText = noteModal.querySelector('#noteText');
        noteText.textContent = note;
    });

    var commentText = document.getElementById('commentText');
    var charCount = document.getElementById('charCount');
    var maxChars = 255;

    // Function to update the character counter
    function updateCharCount() {
        var currentLength = commentText.value.length;
        charCount.textContent = `${currentLength}/${maxChars} characters used`;

        // Prevent user from entering more characters if max is reached
        if (currentLength >= maxChars) {
            commentText.value = commentText.value.substring(0, maxChars);
        }
    }

    // Update character count as the user types
    commentText.addEventListener('input', updateCharCount);
</script>
@endsection
