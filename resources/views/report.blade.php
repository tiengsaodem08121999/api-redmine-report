@extends('layout')

@push('styles')
    <style>
        .trash-action {
            color: darkred;
        }

        .trash-action:hover {
            color: red;
            transition: color 0.3s ease;
            font-size: 1.2rem;
        }
       
    </style>
@endpush
@section('content')
<div class="container-fluid">
    @include('components.modal_confirm_create_report')
    @include('components.modal_log_time', compact('report_summary'))
    </div>
    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Task Log</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th>ID Task</th>
                                    <th>Status</th>
                                    <th>Spent Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $index1 = 1;
                                    $index2 = 1;
                                    $splus = 'Splus.';
                                    $developers = config('information.developer_report');
                                    $isDone = true;
                                @endphp

                                @foreach ($developers as $dev)
                                    <tr>
                                        <td>{{ $index1 }}</td>
                                        <td class="fw-medium">{{ $splus }}{{ $dev }}</td>
                                        <td>
                                            @foreach ($data as $key => $tasks)
                                                @if ($key == $dev)
                                                    @foreach ($tasks as $task)
                                                        @php
                                                            $taskContent = is_array($task['task']) ? implode(' | ', $task['task']) : $task['task'];
                                                        @endphp
                                                        @if(array_key_exists($dev, config('information.user_for_key')))
                                                            <form action="{{ route('delete_spent_time') }}" id="form_delete_spent_time" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $task['id'] }}">
                                                                <input type="hidden" name="dev" value="{{ $dev }}">
                                                                <input type="hidden" name="date" value=" {{request()->date}} ">
                                                            </form>
                                                            <div class="mb-1 text-break">
                                                                {{ $taskContent }}
                                                                <button type="submit" class="btn"> <i class="fa-solid fa-trash trash-action"></i> </button>
                                                            </div>
                                                        @else
                                                            <div class="mb-1 text-break">
                                                                {{ $taskContent }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($data as $key => $tasks)
                                                @if ($key == $dev)
                                                    @foreach ($tasks as $task)
                                                        @php
                                                            $statusMap = [
                                                                'Closed' => 'success',
                                                                'Resolved' => 'success',
                                                                'New' => 'danger',
                                                                'In Progress' => 'primary',
                                                                'Pending' => 'warning',
                                                            ];
                                                            $Status = is_array($task['status']) ? implode(' | ', $task['status']) : $task['status'];
                                                            $badgeClass = $statusMap[$Status] ?? 'secondary';
                                                        @endphp
                                                        <div class="mb-1">
                                                            <span class="badge bg-{{ $badgeClass }}">
                                                                {{ $Status }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($data as $key => $tasks)
                                                @if ($key == $dev)
                                                    @php $spent_time = 0; @endphp
                                                    @foreach ($tasks as $task)
                                                        @php
                                                            $spent_time += $task['spent_time'];
                                                        @endphp
                                                    @endforeach
                                                    <span class="badge bg-info">{{$spent_time}} h</span>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @php $index1++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>
            <div class="card-footer text-end">    
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-{{$isDone ?  'success' : 'danger'}}" data-bs-toggle="modal" data-bs-target="#comfirmCreateReportModal">
                    Create Report
                </button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#delete_spent_time').on('click', function(e) {
                $('#form_delete_spent_time').submit();
            });
        });
    </script>   
@endpush