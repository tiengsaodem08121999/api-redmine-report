@extends('layout')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="container-fluid">
        <form action="{{ route('execute_logtime_for_this_month') }}" method="post">
            @csrf
            <div class="row">
              
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Select developer</h5>
                            <select class="form-select" name="developer" aria-label="Default select example">
                                @foreach (config('information.user_for_key') as $key => $value)
                                    <option value="{{ $value }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    @if(session('taskSuccess'))
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h5>Task success: 
                                    @foreach (session('taskSuccess') as $task)
                                        {{ $task }}
                                    @endforeach
                                </h5>
                            </div>
                        </div>
                    @endif
                    @if(session('taskErrors'))
                        <div class="alert alert-danger">
                            <h5>Task errors: 
                                @foreach (session('taskErrors') as $date => $task)
                                    {{ $date }} - {{ $task }} <br>
                                @endforeach
                            </h5>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Logtime</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="20%">Task #Id</th>
                                        <th width="20%">Spent Time</th>
                                        <th width="15%">Activity</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workingThisMonth as $date)
                                        @php
                                            $day = Carbon::parse($date)->addDay()->day;
                                            if ($day > 16) {
                                                continue;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $date }}</td>
                                            <td><input type="text" name="{{ $date }}[task_id]" class="form-control"
                                                    placeholder="Enter task"></td>
                                            <td><input type="text" name="{{ $date }}[spent_time]" value="8" class="form-control"
                                                    placeholder="hours"></td>
                                            <td>    
                                                <select name="{{ $date }}[activity_id]" class="form-select" required>
                                                    <option value="15">01_Study</option>
                                                    <option value="8">02_Design</option>
                                                    <option value="10" selected>03_Coding</option>
                                                    <option value="9">04_Unit Test</option>
                                                    <option value="17">05_Integration Test</option>
                                                    <option value="11">06_User Acceptance Test</option>
                                                    <option value="16">07_Review (code, doc)</option>
                                                    <option value="24">08_Correction (fix bug, doc)</option>
                                                    <option value="12">09_Translation</option>
                                                    <option value="14">10_Meeting</option>
                                                    <option value="20">11_Training</option>
                                                    <option value="31">12_System Test</option>
                                                    <option value="23">99_Others</option>
                                                </select>
                                            </td>
                                            <td>
                                                <i class="fa-regular fa-square-plus" onclick="addTask({{ $date }})"></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Logtime</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="20%">Task #Id</th>
                                        <th width="20%">Spent Time</th>
                                        <th width="20%">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workingThisMonth as $date)
                                        @php
                                            $day = Carbon::parse($date)->addDay()->day;
                                            if ($day < 17) {
                                                continue;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $date }}</td>
                                            <td><input type="text" name="{{ $date }}[task_id]" class="form-control"
                                                    placeholder="Enter task"></td>
                                            <td><input type="text" name="{{ $date }}[spent_time]" value="8" class="form-control"
                                                    placeholder="hours"></td>
                                            <td>    
                                                <select name="{{ $date }}[activity_id]" class="form-select" required>
                                                    <option value="15">01_Study</option>
                                                    <option value="8">02_Design</option>
                                                    <option value="10" selected>03_Coding</option>
                                                    <option value="9">04_Unit Test</option>
                                                    <option value="17">05_Integration Test</option>
                                                    <option value="11">06_User Acceptance Test</option>
                                                    <option value="16">07_Review (code, doc)</option>
                                                    <option value="24">08_Correction (fix bug, doc)</option>
                                                    <option value="12">09_Translation</option>
                                                    <option value="14">10_Meeting</option>
                                                    <option value="20">11_Training</option>
                                                    <option value="31">12_System Test</option>
                                                    <option value="23">99_Others</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 float-end">Submit</button>  
                </div>
            </div>
        </form>
    </div>
@endsection
