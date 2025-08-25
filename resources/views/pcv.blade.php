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
    <!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if (session('taskSuccess'))
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <h5>Task success: <br>
                            @if(count(session('taskSuccess')) > 0)
                                @foreach (session('taskSuccess') as $task)
                                    {{ $task }} <br>
                                @endforeach
                            @else
                                No tasks were successfully processed.
                            @endif
                        </h5>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">PCV Report</h5>
                </div>  
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Task</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $task)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $task['id'] }}</td>
                                        <td>{{ $task['tracker']['name'] .' #'. $task['id'].':' . $task['subject'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form action="Update_PCV" method="POST" class="mt-3 text-end ">
                        @csrf
                        <button type="submit" class="btn btn-primary">Update PCV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
   
@endpush
