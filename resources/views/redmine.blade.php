@extends('layout')

@section('content')
<div class="container-fluid">
    @include('component.modal_confirm_create_report')
    @include('component.modal_log_time')
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
                                    $developers = [
                                        'VinhDV', 'QuyLV', 'KietNA', 'DuongNT', 'PhuDT', 'YenNH','ThienND','ChuongNPN', 'BaoNC', 'DuyTT', 'NganPVH'
                                    ];
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
                                                        <div class="mb-1 text-break">{{ $taskContent }}</div>
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
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#comfirmCreateReportModal">
                    Create Report
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logTimeModal1">
                    Log Time
                </button>
            </div>
        </div>
    </div>

    {!! '|_. # |_. 開発者 |_. ID タスク |_. ステータス |_. 備考 |<br>' !!}
    @foreach ($developers as $dev)
        {!! '| ' . $index2  . ' |'. $splus . $dev .'| ' !!}
        @foreach ($data as $key =>  $tasks)
            @if ($key == $dev)
                @foreach ($tasks as $task)
                    @php
                        $taskContent = is_array($task['task']) ? implode(' | ', $task['task']) : $task['task'];
                    @endphp
                    {!! $taskContent  !!}   <br>
                @endforeach
            @endif
        @endforeach
        {!! '|  ' !!}
    
        @foreach ($data as $key =>  $tasks)
        @if ($key == $dev)
                @foreach ($tasks as $task)
                    @php
                        $Status = is_array($task['status']) ? implode(' | ', $task['status']) : $task['status'];
                        $taskStatus = $Status == 'Closed' || $Status == 'Resolved' ? '完了' : '進行中';
                    @endphp
                    {!! $taskStatus  !!} <br>
                @endforeach
            @endif
        @endforeach
        {!!'|. |<br>'  !!}
        @php $index2++; @endphp
    @endforeach
@endsection