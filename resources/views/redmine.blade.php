@extends('layout')
@section('content')
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">Redmine Log Report</h1>
                    <form action="{{ route('redmine') }}" method="get" class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" 
                                name="date" 
                                value="{{ request()->get('date') ?? date('Y-m-d') }}"
                                class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                                <a href="{{ route('timesheet') }}" class="btn btn-primary">
                                    Check Time Sheet
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="">
            <!-- Part 1: Modern Table -->
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Task Log</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID task </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $index1 = 1;
                                        $index2 = 1;
                                        $splus = 'Splus.';
                                        $developers = [
                                            'VinhDV', 'QuyLV', 'KietNA',
                                            'DuongNT', 'PhuDT', 'YenNH',
                                        ];
                                    @endphp
                                    @foreach ($developers as $dev)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $splus }}{{ $dev }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @foreach ($data as $key => $tasks)
                                                    @if ($key == $dev)
                                                        @foreach ($tasks as $task)
                                                            @php
                                                                $taskContent = is_array($task['task']) ? implode(' | ', $task['task']) : $task['task'];
                                                            @endphp
                                                            <div class="mb-1">{{ $taskContent }}</div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </td> 
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @foreach ($data as $key => $tasks)
                                                    @if ($key == $dev)
                                                        @foreach ($tasks as $task)
                                                            @php
                                                                $color = [
                                                                    'Closed' => 'success',
                                                                    'Resolved' => 'success',
                                                                    'New' => 'danger',
                                                                    'In Progress' => 'primary',
                                                                    'Pending' => 'warning',
                                                                ];
                                                                $Status = is_array($task['status']) ? implode(' | ', $task['status']) : $task['status'];
                                                                $statusColor = $Status == 'New' ? 'danger' : 'primary';
                                                            @endphp
                                                            <div class="mb-1">
                                                                <span class="text-{{ $color[$Status] }}">
                                                                    {{ $Status }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"> 
                                                @foreach ($data as $key => $tasks)
                                                @if ($key == $dev)
                                                    @php $spent_time = 0; @endphp
                                                        @foreach ($tasks as $task)
                                                            @php
                                                                $spent_time += $task['spent_time'];
                                                            @endphp
                                                        
                                                        @endforeach
                                                        {{$spent_time}}
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