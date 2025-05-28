@extends('layout')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('taskSuccess'))
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h5>Task success: <br>
                                @foreach (session('taskSuccess') as $task)
                                    {{ $task }}
                                @endforeach
                            </h5>
                        </div>
                    </div>
                @endif
                @if(session('taskErrors'))
                    <div class="alert alert-danger">
                        <h5>Task errors: <br>
                            @foreach (session('taskErrors') as $date => $task)
                                @foreach ($task as $taskId)
                                    {{ $date }} - {{ $taskId }} <br>
                                @endforeach
                            @endforeach
                        </h5>
                    </div>
                @endif
            </div>
        </div>
        <form action="{{ route('execute_logtime_for_this_month') }}" id="form_logtime" method="post">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Select member</h5>
                                    <select class="form-select" name="developer" aria-label="Default select example">
                                        @foreach ($developer as $item)
                                            <option value="{{ $item->key }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Add key member</h5>
                                    <input type="text" name="key" value="{{ old('key') }}" class="form-control @error('key') is-invalid @enderror" placeholder="Enter key">
                                    @error('key')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control mt-2 @error('name') is-invalid @enderror" placeholder="Enter name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <button type="button" class="btn btn-primary mt-2" id="add_key_developer">Add key</button>
                                </div>
                            </div>
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
                                        <th width="15%">Activity</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workingThisMonth as $key => $date)
                                        @php
                                            $index = 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $date }}</td>
                                            <td><input type="text" name="{{ $date }}[{{ $index }}][task_id]" class="form-control"
                                                    placeholder="Enter task"></td>
                                            <td><input type="text" name="{{ $date }}[{{ $index }}][spent_time]" value="8" class="form-control"
                                                    placeholder="hours"></td>
                                            <td>    
                                                <select name="{{ $date }}[{{ $index }}][activity_id]" class="form-select" required>
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
                                                <i class="fa-regular fa-square-plus add_log_time" style="cursor: pointer;"  title="Add new row" data-date="{{ $date }}"></i>
                                                <i class="fa-regular fa-square-minus remove_log_time text-danger" style="cursor: pointer;" title="Remove this row" data-date="{{ $date }}"></i>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary float-end">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.add_log_time', function() {
                const date = $(this).data('date');
                var totalInputsByDate = $('input[name^="'+date+'"]').length;
                var $spentTime = $('input[name^="'+date+'"][name$="[spent_time]"]').last();
                var spentTime = parseFloat($spentTime.val()) || 0;
                $spentTime.val(spentTime / 2);
                const newRow = `
                    <tr>
                        <td>${date}</td>
                        <td><input type="text" name="${date}[${totalInputsByDate}][task_id]" class="form-control" placeholder="Enter task"></td>
                        <td><input type="text" name="${date}[${totalInputsByDate}][spent_time]" value="${spentTime/2}" class="form-control" placeholder="hours"></td>
                        <td>    
                            <select name="${date}[${totalInputsByDate}][activity_id]" class="form-select" required>
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
                            <i class="fa-regular fa-square-plus add_log_time" data-date="${date}"></i>
                            <i class="fa-regular fa-square-minus remove_log_time text-danger" style="cursor: pointer;" title="Remove this row" data-date="${date}"></i>
                        </td>
                    </tr>
                `;

                $(this).closest('tr').after(newRow);
            });

            $(document).on('click', '.remove_log_time', function () {
                const date = $(this).data('date');
                var totalInputsByDate = $('input[name^="'+date+'"]').length;
                console.log(totalInputsByDate);
                if (totalInputsByDate <= 2) {
                    alert('Cannot remove the last row.');
                    return;
                }

                $(this).closest('tr').remove();
            });

            $(document).on('click', '#add_key_developer', function() {
                $('#form_logtime').removeAttr('action');
                $('#form_logtime').attr('action', '{{ route('add_key_developer') }}');
                $('#form_logtime').submit();
            });
        });
    </script>
@endpush
