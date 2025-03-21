<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redmine Log Report</title>
</head>
<body>

<h2>Redmine Log Report</h2>
<form action="{{ route('redmine') }}" method="get">
    <input type="date" name="date" value="{{ request()->get('date') ?? date('Y-m-d') }}">
    <button type="submit">Submit</button>
</form>
@php
    $index = 1;
    $splus = 'Splus.';
    $developers = [
        'VinhDV', 'DuyTT', 'QuyLV', 'KietNA', 'HaiLT',
        'DuongNT', 'ChuongNPN', 'PhuDT', 'YenNH', 'ThienND'
    ];
@endphp

<h3> check log time </h3>
<table border="1">
    <tr>
        <th>User</th>
        <th>Task</th>
        <th>Status</th>
    </tr>
    @foreach ($developers as $dev)
        <tr>
            <td>{{ $dev }}  </td>
            <td>
                @foreach ($data as $key => $tasks)
                    @if ($key == $dev)
                        @foreach ($tasks as $task)
                            {{ $task['task'] }} <br>
                        @endforeach
                    @endif
                @endforeach
                    </td>
            <td>
                @foreach ($data as $key => $tasks)
                    @if ($key == $dev)
                        @foreach ($tasks as $task)
                            {{ $task['status'] }} <br>
                        @endforeach
                    @endif
                @endforeach
            </td>
        </tr>
    @endforeach
</table>    

<br>


{!! '|_. # |_. 開発者 |_. ID タスク |_. ステータス |_. 備考 |<br>' !!}


@foreach ($developers as $dev)
    {!! '| ' . $index . ' |'. $splus . $dev .'| ' !!}
    @foreach ($data as $key =>  $tasks)
        @if ($key == $dev)
            @foreach ($tasks as $task)
                @php
                    $taskContent = is_array($task['task']) ? implode(' | ', $task['task']) : $task['task'];
                @endphp
                {!! $taskContent  !!} <br>
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
    @php $index++; @endphp
@endforeach

</body>
</html>
