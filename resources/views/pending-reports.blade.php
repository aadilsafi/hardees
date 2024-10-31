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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitCommentButton">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-lg fade" id="missing_reportsModal" tabindex="-1" role="dialog"
        aria-labelledby="missing_reportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="w-100 display-6 text-center fw-bold" id="missing_reportsModalLabel">Missing Schedule
                        Reports</h3>
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
                                <td>
                                    {{ $report['missing_file'] }}
                                    @if($report['on_db'] ?? false)
                                    <i class="fa fa-info-circle text-danger ml-1"
                                        style="font-size:18px; cursor: pointer;" data-toggle="tooltip"
                                        data-placement="top"
                                        title="PDF copy of Schedule not available. Something must have happened during upload. If the schedule has not been published, you can ask the store to resubmit."></i>
                                    @endif
                                </td>
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
                                <th>-</th>
                                <th>-</th>
                                <th width="28%">Schedule Name</th>

                            </thead>

                            <tbody>

                                @foreach($reports as $report )
                                <tr>


                                    <td class="align-middle">
                                        <form action="{{ route('report.toggle', $report->ID)}}" method="POST">
                                            @csrf
                                            @method('put')
                                            <button
                                                class="btn {{$report->Approved ? 'btn-danger'  :'btn-primary'}} btn-sm"
                                                type="submit">{{$report->Approved ? 'Revoke' :'Approve'}}</button>
                                        </form>
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
                                    <td @class(['align-middle', 'text-danger font-weight-bold'=>
                                        $report->LaborHrsOverUnder > 0
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

                                    <!-- The random number at the end of the PDF is to force it to use a new version of the file and not load a cached version on page reload/load  -->
                                    <td class="align-middle">
                                        @php
                                        $fileExists = Storage::disk('pdfs')->exists("{$report->UnitNo}/{$report->ScheduleName}");
                                        @endphp
                                        @if ($fileExists)
                                        <a href="{{ route('download.pdf', ['unit' => $report->UnitNo, 'filename' => $report->ScheduleName]) }}"
                                            target="_blank" style="text-decoration:underline;cursor: pointer">
                                            {{$report->ScheduleName}}
                                        </a>
                                        @else
                                        <a href="#" class="text-danger"
                                            style="text-decoration:underline;cursor: pointer;" data-bs-toggle="modal"
                                            data-bs-target="#fileNotFoundModal" data-toggle="tooltip"
                                            data-placement="top"
                                            title="Schedule not available. Something must have happened during upload. If the schedule has not been published, you can ask the store to resubmit.">
                                            {{$report->ScheduleName}}
                                        </a>
                                        @endif
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
<!-- Bootstrap Modal Structure -->
<div class="modal fade" id="fileNotFoundModal" tabindex="-1" role="dialog" aria-labelledby="fileNotFoundModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileNotFoundModalLabel">Schedule Not Available</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                Something must have happened during upload. If the schedule has not been published, you can ask the
                store to resubmit.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
<script>
    // Listen for the modal show event to set the comment
     var commentModal = document.getElementById('commentsModal');
    commentModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var comment = button.getAttribute('data-comment');
        var id = button.getAttribute('data-id');
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
